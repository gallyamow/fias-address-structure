<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\ActualityComparator;
use Addresser\AddressRepository\Address;
use Addresser\AddressRepository\AddressBuilderInterface;
use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressSynonymizer;
use Addresser\AddressRepository\Exceptions\AddressBuildFailedException;
use Addresser\AddressRepository\AddressLevelSpec;

/**
 * Формирует адрес на основе данных из ФИАС.
 * Работает только со структурой которую возвращает Finder.
 */
class FiasAddressBuilder implements AddressBuilderInterface
{
    private AddressLevelSpecResolverInterface $addrObjectTypeNameResolver;
    private AddressLevelSpecResolverInterface $houseTypeNameResolver;
    private AddressLevelSpecResolverInterface $addHouseTypeNameResolver;
    private AddressLevelSpecResolverInterface $apartmentTypeNameResolver;
    private AddressLevelSpecResolverInterface $roomTypeNameResolver;
    private ActualityComparator $actualityPeriodComparator;
    private AddressSynonymizer $addressSynonymizer;

    public function __construct(
        AddressLevelSpecResolverInterface $addrObjectTypeNameResolver,
        AddressLevelSpecResolverInterface $houseTypeNameResolver,
        AddressLevelSpecResolverInterface $addHouseTypeNameResolver,
        AddressLevelSpecResolverInterface $apartmentTypeNameResolver,
        AddressLevelSpecResolverInterface $roomTypeNameResolver,
        ActualityComparator $actualityPeriodComparator,
        AddressSynonymizer $addressSynonymizer
    ) {
        $this->addrObjectTypeNameResolver = $addrObjectTypeNameResolver;
        $this->houseTypeNameResolver = $houseTypeNameResolver;
        $this->addHouseTypeNameResolver = $addHouseTypeNameResolver;
        $this->apartmentTypeNameResolver = $apartmentTypeNameResolver;
        $this->roomTypeNameResolver = $roomTypeNameResolver;
        $this->actualityPeriodComparator = $actualityPeriodComparator;
        $this->addressSynonymizer = $addressSynonymizer;
    }

    public function build(array $data, ?Address $existsAddress = null): Address
    {
        $hierarchyId = (int)$data['hierarchy_id'];

        $parents = json_decode($data['parents'], true, 512, JSON_THROW_ON_ERROR);

        // группируем по уровням
        $parentsByLevels = [];
        foreach ($parents as $k => $item) {
            $relation = $item['relation'];
            [$addressLevel,] = $this->resolveLevels($relation);
            $parentsByLevels[$addressLevel] = $parentsByLevels[$addressLevel] ?? [];
            $parentsByLevels[$addressLevel][] = $item;
        }

        // мы должны сохранить изменения внесенные другиги builder
        $address = $existsAddress ?? new Address();

        foreach ($parentsByLevels as $addressLevel => $levelItems) {
            // находим актуальное значение
            $actualItem = array_values(
                array_filter(
                    $levelItems,
                    static function ($item) {
                        return $item['relation']['relation_is_active'] && $item['relation']['relation_is_actual'];
                    }
                )
            );
            if (count($actualItem) > 1) {
                throw new AddressBuildFailedException(
                    sprintf('Several %d actual relation found on one level %d', count($actualItem), $addressLevel),
                );
            }
            $actualItem = $actualItem[0];

            $relation = $actualItem['relation'];
            [, $fiasLevel] = $this->resolveLevels($relation);
            $relationData = $relation['relation_data'];

            $actualParams = $this->resolveActualParams(
                $actualItem['params'] ?? [],
                [FiasParamType::KLADR, FiasParamType::OKATO, FiasParamType::OKTMO, FiasParamType::POSTAL_CODE]
            );

            $kladrId = $actualParams[FiasParamType::KLADR]['value'] ?? null;
            $okato = $actualParams[FiasParamType::OKATO]['value'] ?? null;
            $oktmo = $actualParams[FiasParamType::OKTMO]['value'] ?? null;
            $postalCode = $actualParams[FiasParamType::POSTAL_CODE]['value'] ?? null;

            $fiasId = null;
            switch ($addressLevel) {
                case AddressLevel::REGION:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    if (null === $fiasId || empty($name) || null === $kladrId) {
                        throw new AddressBuildFailedException('Null values for region');
                    }

                    $address->setRegionFiasId($fiasId);
                    $address->setRegionKladrId($kladrId);

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
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

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
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

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
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

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
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

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
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

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
                    $address->setHouseType($typeName->getShortName());
                    $address->setHouseTypeFull($typeName->getName());

                    $address->setHouse($this->prepareString($relationData['housenum']));

                    $address->setBlock1($relationData['addnum1'] ?? null);
                    if ($relationData['addtype1']) {
                        $blockTypeName = $this->resolveHouseBlockSpec((int)$relationData['addtype1']);

                        $address->setBlockType1($blockTypeName->getShortName());
                        $address->setBlockTypeFull1($blockTypeName->getName());
                    }

                    $address->setBlock2($relationData['addnum2'] ?? null);
                    if ($relationData['addtype2']) {
                        $blockTypeName = $this->resolveHouseBlockSpec((int)$relationData['addtype2']);

                        $address->setBlockType2($blockTypeName->getShortName());
                        $address->setBlockTypeFull2($blockTypeName->getName());
                    }
                    break;
                case AddressLevel::FLAT:
                    $fiasId = $relationData['objectguid'];
                    $address->setFlatFiasId($fiasId);

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
                    $address->setFlatType($typeName->getShortName());
                    $address->setFlatTypeFull($typeName->getName());

                    $address->setFlat($this->prepareString($relationData['number']));
                    break;
                case AddressLevel::ROOM:
                    $fiasId = $relationData['objectguid'];
                    $address->setRoomFiasId($fiasId);

                    $typeName = $this->resolveLevelSpec($addressLevel, $relationData);
                    $address->setRoomType($typeName->getShortName());
                    $address->setRoomTypeFull($typeName->getName());

                    $address->setRoom($this->prepareString($relationData['number']));
                    break;
                case AddressLevel::STEAD:
                    $fiasId = $relationData['objectguid'];
                    // TODO: Пока не решили хранить ли
                    // $address->setSteadFiasId($fiasId);
                    // $address->setStead($this->prepareString($relationData['number']));
                    break;
                case AddressLevel::CAR_PLACE:
                    $fiasId = $relationData['objectguid'];
                    // TODO: Пока не решили хранить ли
                    // $address->setCarPlaceFiasId($fiasId);
                    // $address->setCarPlace($this->prepareString($relationData['number']));
                    break;
            }

            // данные последнего уровня
            if ($addressLevel === \array_key_last($parentsByLevels)) {
                if (null === $fiasId) {
                    throw new AddressBuildFailedException('Null value for fiasId');
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

    private function resolveLevelSpec(int $addressLevel, array $relationData): AddressLevelSpec
    {
        $typeName = null;

        switch ($addressLevel) {
            case AddressLevel::REGION:
            case AddressLevel::AREA:
            case AddressLevel::CITY:
            case AddressLevel::SETTLEMENT:
            case AddressLevel::STREET:
                return $this->addrObjectTypeNameResolver->resolve($addressLevel, $relationData['typename']);
            case AddressLevel::HOUSE:
                // Респ Башкортостан, г Кумертау, ул Брикетная, влд 5 к А стр 1/6
                return $this->houseTypeNameResolver->resolve(AddressLevel::HOUSE, (int)$relationData['housetype']);
            case AddressLevel::FLAT:
                return $this->apartmentTypeNameResolver->resolve(
                    AddressLevel::FLAT,
                    (int)$relationData['aparttype']
                );
            case AddressLevel::ROOM:
                return $this->roomTypeNameResolver->resolve(AddressLevel::ROOM, (int)$relationData['roomtype']);
        }

        throw new AddressBuildFailedException(
            sprintf('AddressLevel %d has no type', $addressLevel)
        );
    }

    private function resolveHouseBlockSpec(int $addType): AddressLevelSpec
    {
        return $this->addHouseTypeNameResolver->resolve(AddressLevel::HOUSE, $addType);
    }

    private function resolveLevels(array $item): array
    {
        $relationType = $item['relation_type'];

        $addressLevel = null;
        $fiasLevel = null;

        switch ($relationType) {
            case FiasRelationType::ADDR_OBJ:
                $fiasLevel = (int)$item['relation_data']['level'];
                $addressLevel = FiasLevel::mapToAddressLevel($fiasLevel);
                break;
            case FiasRelationType::HOUSE:
                $addressLevel = AddressLevel::HOUSE;
                $fiasLevel = FiasLevel::BUILDING;
                break;
            case FiasRelationType::APARTMENT:
                $addressLevel = AddressLevel::FLAT;
                $fiasLevel = FiasLevel::PREMISES;
                break;
            case FiasRelationType::ROOM:
                $addressLevel = AddressLevel::ROOM;
                $fiasLevel = FiasLevel::PREMISES_WITHIN_THE_PREMISES;
                break;
            case FiasRelationType::CAR_PLACE:
                $addressLevel = AddressLevel::CAR_PLACE;
                $fiasLevel = FiasLevel::CAR_PLACE;
                break;
            case FiasRelationType::STEAD:
                $addressLevel = AddressLevel::STEAD;
                $fiasLevel = FiasLevel::STEAD;
                break;
        }

        return [$addressLevel, $fiasLevel];
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
