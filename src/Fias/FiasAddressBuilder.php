<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\ActualityComparator;
use Addresser\AddressRepository\Address;
use Addresser\AddressRepository\AddressBuilderInterface;
use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressSynonymizer;
use Addresser\AddressRepository\Exceptions\AddressBuildFailedException;
use Addresser\AddressRepository\Exceptions\InvalidAddressLevelException;
use Addresser\AddressRepository\Exceptions\RuntimeException;

/**
 * Формирует адрес на основе данных из ФИАС.
 * Работает только со структурой которую возвращает Finder.
 */
class FiasAddressBuilder implements AddressBuilderInterface
{
    private ObjectAddressLevelSpecResolverInterface $addrObjectTypeNameResolver;
    private TypeAddressLevelSpecResolverInterface $houseSpecResolver;
    private TypeAddressLevelSpecResolverInterface $addHouseSpecResolver;
    private TypeAddressLevelSpecResolverInterface $apartmentSpecResolver;
    private TypeAddressLevelSpecResolverInterface $roomSpecResolver;
    private ActualityComparator $actualityPeriodComparator;
    private AddressSynonymizer $addressSynonymizer;

    public function __construct(
        ObjectAddressLevelSpecResolverInterface $addrObjectTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $houseTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $addHouseTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $apartmentTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $roomTypeNameResolver,
        ActualityComparator $actualityPeriodComparator,
        AddressSynonymizer $addressSynonymizer
    ) {
        $this->addrObjectTypeNameResolver = $addrObjectTypeNameResolver;
        $this->houseSpecResolver = $houseTypeNameResolver;
        $this->addHouseSpecResolver = $addHouseTypeNameResolver;
        $this->apartmentSpecResolver = $apartmentTypeNameResolver;
        $this->roomSpecResolver = $roomTypeNameResolver;
        $this->actualityPeriodComparator = $actualityPeriodComparator;
        $this->addressSynonymizer = $addressSynonymizer;
    }

    // todo: too huge loop
    public function build(array $data, ?Address $existsAddress = null): Address
    {
        $hierarchyId = (int)$data['hierarchy_id'];
        $objectId = (int)$data['object_id'];

        $parents = json_decode($data['parents'], true, 512, JSON_THROW_ON_ERROR);

        /**
         * группируем по уровням ФИАС, так как дополнительные локаций таких как СНТ, ГСК mapped на один и тот же
         * уровень \Addresser\AddressRepository\AddressLevel::SETTLEMENT - может быть несколько актуальных значений.
         */
        $groupedParents = [];
        foreach ($parents as $k => $item) {
            $fiasLevel = $this->resolveFiasLevel($item['relation']);

            $groupedParents[$fiasLevel] = $groupedParents[$fiasLevel] ?? [];
            $groupedParents[$fiasLevel][] = $item;
        }

        // мы должны сохранить изменения внесенные другими builder
        $address = $existsAddress ?? new Address();

        foreach ($groupedParents as $fiasLevel => $levelItems) {
            // находим актуальное значение
            $actualItem = array_values(
                array_filter(
                    $levelItems,
                    static function ($item) {
                        return $item['relation']['relation_is_active'] && $item['relation']['relation_is_actual'];
                    }
                )
            );

            /**
             * Здесь мы должны выделить доп. территории и заполнить ими поля.
             */
            if (count($actualItem) > 1) {
                throw AddressBuildFailedException::withIdentifier(
                    'object_id',
                    $objectId,
                    sprintf('There are "%d" actual relations for one fias level "%d"', count($actualItem), $fiasLevel),
                );
            }

            $actualItem = $actualItem[0];
            $actualParams = $this->resolveActualParams(
                $actualItem['params'] ?? [],
                [FiasParamType::KLADR, FiasParamType::OKATO, FiasParamType::OKTMO, FiasParamType::POSTAL_CODE]
            );

            $kladrId = $actualParams[FiasParamType::KLADR]['value'] ?? null;
            $okato = $actualParams[FiasParamType::OKATO]['value'] ?? null;
            $oktmo = $actualParams[FiasParamType::OKTMO]['value'] ?? null;
            $postalCode = $actualParams[FiasParamType::POSTAL_CODE]['value'] ?? null;

            $fiasId = null;
            $addressLevel = FiasLevel::mapAdmHierarchyToAddressLevel($fiasLevel);
            $relationData = $actualItem['relation']['relation_data'];

            switch ($addressLevel) {
                case AddressLevel::REGION:
                    $fiasId = $relationData['objectguid'];
                    if (empty($fiasId)) {
                        throw AddressBuildFailedException::withIdentifier(
                            'object_id',
                            $objectId,
                            sprintf('Empty fiasId for region level.'),
                        );
                    }

                    $name = $relationData['name'];
                    if (empty($name)) {
                        throw AddressBuildFailedException::withIdentifier(
                            'object_id',
                            $objectId,
                            sprintf('Empty name for region level.'),
                        );
                    }

                    $address->setRegionFiasId($fiasId);
                    $address->setRegionKladrId($kladrId);

                    $typeName = $this->addrObjectTypeNameResolver->resolve($fiasLevel, $relationData['typename']);
                    $address->setRegionType($typeName->getShortName());
                    $address->setRegionTypeFull($typeName->getName());

                    $address->setRegion($this->prepareString($name));
                    // учитываем переименование регионов
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::AREA:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setAreaFiasId($fiasId);
                    $address->setAreaKladrId($kladrId);

                    $typeName = $this->addrObjectTypeNameResolver->resolve($fiasLevel, $relationData['typename']);
                    $address->setAreaType($typeName->getShortName());
                    $address->setAreaTypeFull($typeName->getName());

                    $address->setArea($this->prepareString($name));
                    // учитываем переименование районов
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::CITY:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setCityFiasId($fiasId);
                    $address->setCityKladrId($kladrId);

                    $typeName = $this->addrObjectTypeNameResolver->resolve($fiasLevel, $relationData['typename']);
                    $address->setCityType($typeName->getShortName());
                    $address->setCityTypeFull($typeName->getName());

                    $address->setCity($this->prepareString($name));
                    // учитываем переименование городов
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::SETTLEMENT:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setSettlementFiasId($fiasId);
                    $address->setSettlementKladrId($kladrId);

                    $typeName = $this->addrObjectTypeNameResolver->resolve($fiasLevel, $relationData['typename']);
                    $address->setSettlementType($typeName->getShortName());
                    $address->setSettlementTypeFull($typeName->getName());

                    $address->setSettlement($this->prepareString($name));
                    // учитываем переименование поселений
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::STREET:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setStreetFiasId($fiasId);
                    $address->setStreetKladrId($kladrId);

                    $typeName = $this->addrObjectTypeNameResolver->resolve($fiasLevel, $relationData['typename']);
                    $address->setStreetType($typeName->getShortName());
                    $address->setStreetTypeFull($typeName->getName());

                    $address->setStreet($this->prepareString($name));
                    // учитываем переименование улиц
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::HOUSE:
                    $fiasId = $relationData['objectguid'];
                    $address->setHouseFiasId($fiasId);
                    $address->setHouseKladrId($kladrId);

                    $typeName = $this->houseSpecResolver->resolve((int)$relationData['housetype']);
                    $address->setHouseType($typeName->getShortName());
                    $address->setHouseTypeFull($typeName->getName());

                    $address->setHouse($this->prepareString($relationData['housenum']));

                    $address->setBlock1($relationData['addnum1'] ?? null);
                    if ($relationData['addtype1']) {
                        $blockTypeName = $this->addHouseSpecResolver->resolve((int)$relationData['addtype1']);
                        $address->setBlockType1($blockTypeName->getShortName());
                        $address->setBlockTypeFull1($blockTypeName->getName());
                    }

                    $address->setBlock2($relationData['addnum2'] ?? null);
                    if ($relationData['addtype2']) {
                        $blockTypeName = $this->addHouseSpecResolver->resolve((int)$relationData['addtype2']);
                        $address->setBlockType2($blockTypeName->getShortName());
                        $address->setBlockTypeFull2($blockTypeName->getName());
                    }
                    break;
                case AddressLevel::FLAT:
                    $fiasId = $relationData['objectguid'];
                    $address->setFlatFiasId($fiasId);

                    $typeName = $this->apartmentSpecResolver->resolve((int)$relationData['aparttype']);
                    $address->setFlatType($typeName->getShortName());
                    $address->setFlatTypeFull($typeName->getName());

                    $address->setFlat($this->prepareString($relationData['number']));
                    break;
                case AddressLevel::ROOM:
                    $fiasId = $relationData['objectguid'];
                    $address->setRoomFiasId($fiasId);

                    $typeName = $this->roomSpecResolver->resolve((int)$relationData['roomtype']);
                    $address->setRoomType($typeName->getShortName());
                    $address->setRoomTypeFull($typeName->getName());

                    $address->setRoom($this->prepareString($relationData['number']));
                    break;
                case AddressLevel::STEAD:
                case AddressLevel::CAR_PLACE:
                    // эти уровни не индексируем, таким образом сюда они попадать не должны
                    throw new InvalidAddressLevelException(sprintf('Unsupported address level "%d".', $addressLevel));
            }

            // данные последнего уровня
            // здесь пользуемся тем что $fiasLevel уникальный
            // TODO: подумать о повторах для СНТ
            if ($fiasLevel === \array_key_last($groupedParents)) {
                if (null === $fiasId) {
                    throw AddressBuildFailedException::withIdentifier(
                        'object_id',
                        $objectId,
                        sprintf('Empty fiasId for region level.'),
                    );
                }

                $address->setFiasId($fiasId);
                $address->setAddressLevel($addressLevel);
                $address->setFiasLevel($fiasLevel);
                $address->setFiasHierarchyId($hierarchyId);
                $address->setOkato($okato ?? null);
                $address->setOktmo($oktmo ?? null);
                $address->setPostalCode($postalCode ?? null);
                $address->setKladrId($kladrId ?? null);
                $address->setSynonyms($this->addressSynonymizer->getSynonyms($fiasId));
            }
        }

        return $address;
    }

    private function resolveLevelRenaming(array $levelItems, string $currentName, string $nameField = 'name'): array
    {
        $notActualItems = array_values(
            array_filter(
                $levelItems,
                static function ($item) {
                    return !($item['relation']['relation_is_active'] && $item['relation']['relation_is_actual']);
                }
            )
        );

        return array_values(
            array_filter(
                array_unique(
                    array_map(
                        static function ($item) use ($nameField) {
                            return $item['relation']['relation_data'][$nameField];
                        },
                        $notActualItems
                    )
                ),
                static function ($name) use ($currentName) {
                    return $name !== $currentName;
                }
            )
        );
    }

    /**
     * Поле 'level' есть только в таблице addr_obj.
     * Соответственно для остальных таблиц мы определяем его по relation_type.
     *
     * @param array $relation
     * @return int
     */
    private function resolveFiasLevel(array $relation): int
    {
        $relationType = $relation['relation_type'];

        switch ($relationType) {
            case FiasRelationType::ADDR_OBJ:
                return (int)$relation['relation_data']['level'];
            case FiasRelationType::HOUSE:
                return FiasLevel::BUILDING;
            case FiasRelationType::APARTMENT:
                return FiasLevel::PREMISES;
            case FiasRelationType::ROOM:
                return FiasLevel::PREMISES_WITHIN_THE_PREMISES;
            case FiasRelationType::CAR_PLACE:
                return FiasLevel::CAR_PLACE;
            case FiasRelationType::STEAD:
                return FiasLevel::STEAD;
        }

        throw new RuntimeException(sprintf('Failed to resolve FiasLevel by relation_type "%s"', $relationType));
    }

    private function resolveActualParams(array $groupedHierarchyParams, array $keys): array
    {
        $res = [];
        $currentDate = date('Y-m-d');

        foreach ($groupedHierarchyParams as $hierarchyParam) {
            foreach ($hierarchyParam['values'] as $valueItem) {
                $typeId = $valueItem['type_id'];

                if (in_array($typeId, $keys, true)) {
                    // сразу пропускаем неактуальные
                    if ($valueItem['end_date'] < $currentDate) {
                        continue;
                    }

                    $oldValueItem = $res[$typeId] ?? null;

                    if (null === $oldValueItem
                        || ($oldValueItem && $this->actualityPeriodComparator->compare(
                                $oldValueItem['start_date'],
                                $oldValueItem['end_date'],
                                $valueItem['start_date'],
                                $valueItem['end_date']
                            ) === -1)
                    ) {
                        // обновляем только если новое значении более актуальное чем старое
                        $res[$typeId] = $valueItem;
                    }
                }
            }
        }

        return $res;
    }

    private function prepareString(?string $s): ?string
    {
        if (null === $s) {
            return null;
        }
        $tmp = trim($s);

        return empty($tmp) ? null : $tmp;
    }
}
