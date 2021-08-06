<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\ActualityComparator;
use Addresser\AddressRepository\Address;
use Addresser\AddressRepository\AddressBuilderInterface;
use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
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
    private ObjectAddressLevelSpecResolverInterface $addrObjectSpecResolver;
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
        $this->addrObjectSpecResolver = $addrObjectTypeNameResolver;
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
         * Группируем по AddressLevel. Так как дополнительные локаций таких как СНТ, ГСК mapped на один и тот же
         * уровень AddressLevel::SETTLEMENT может быть несколько актуальных значений.
         */
        $groupedParents = [];
        foreach ($parents as $k => $item) {
            $addressLevel = $this->resolveAddressLevel($item['relation']);

            $groupedParents[$addressLevel] = $groupedParents[$addressLevel] ?? [];
            $groupedParents[$addressLevel][] = $item;
        }

        // мы должны сохранить изменения внесенные другими builder
        $address = $existsAddress ?? new Address();
        $actualNaming = null;

        foreach ($groupedParents as $addressLevel => $levelParents) {
            // находим актуальное значение
            $actualParents = array_values(
                array_filter(
                    $levelParents,
                    static function ($item) {
                        return $item['relation']['relation_is_active'] && $item['relation']['relation_is_actual'];
                    }
                )
            );

            /**
             * Все relation на одном уровне AddressLevel неактивные.
             * Такое было ранее при группировке по FiasLevel (для устаревших уровней, например ADDITIONAL_TERRITORIES_LEVEL),
             * при группировке по AddressLevel - такого быть не должно поэтому бросаем exception.
             */
            if (count($actualParents) === 0) {
                throw AddressBuildFailedException::withIdentifier(
                    'object_id',
                    $objectId,
                    sprintf('There are no actual relations for one address level "%d"', $addressLevel),
                );
            }

            /**
             * Есть несколько активных relations на одном уровне AddressLevel.
             * Надо решить как их объединять либо взять из них какой то 1
             */
            if (count($actualParents) > 1) {
                throw AddressBuildFailedException::withIdentifier(
                    'object_id',
                    $objectId,
                    sprintf(
                        'There are "%d" actual relations for one address level "%d"',
                        count($actualParents),
                        $addressLevel
                    ),
                );
            }

            $mainParent = $actualParents[0];
            $mainRelation = $mainParent['relation'];
            $mainRelationData = $mainRelation['relation_data'];

            $fiasLevel = $this->resolveFiasLevel($mainRelation);

            $actualParams = $this->resolveActualParams(
                $mainParent['params'] ?? [],
                [FiasParamType::KLADR, FiasParamType::OKATO, FiasParamType::OKTMO, FiasParamType::POSTAL_CODE]
            );

            $kladrId = $actualParams[FiasParamType::KLADR]['value'] ?? null;
            $okato = $actualParams[FiasParamType::OKATO]['value'] ?? null;
            $oktmo = $actualParams[FiasParamType::OKTMO]['value'] ?? null;
            $postalCode = $actualParams[FiasParamType::POSTAL_CODE]['value'] ?? null;

            $fiasId = null;


            switch ($addressLevel) {
                case AddressLevel::REGION:
                    $fiasId = $mainRelationData['objectguid'];
                    if (empty($fiasId)) {
                        throw AddressBuildFailedException::withIdentifier(
                            'object_id',
                            $objectId,
                            sprintf('Empty fiasId for region level.'),
                        );
                    }

                    $name = $this->prepareString($mainRelationData['name']);
                    if (empty($name)) {
                        throw AddressBuildFailedException::withIdentifier(
                            'object_id',
                            $objectId,
                            sprintf('Empty name for region level.'),
                        );
                    }

                    $address->setRegionFiasId($fiasId);
                    $address->setRegionKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setRegionType($levelSpec->getShortName());
                    $address->setRegionTypeFull($levelSpec->getName());

                    $address->setRegion($name);
                    $address->setRegionWithType($this->resolveWithTypeName($name, $levelSpec));

                    // учитываем переименование регионов
                    $actualNaming = $name;
                    break;
                case AddressLevel::AREA:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->prepareString($mainRelationData['name']);

                    $address->setAreaFiasId($fiasId);
                    $address->setAreaKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setAreaType($levelSpec->getShortName());
                    $address->setAreaTypeFull($levelSpec->getName());

                    $address->setArea($name);
                    $address->setAreaWithType($this->resolveWithTypeName($name, $levelSpec));

                    // учитываем переименование районов
                    $actualNaming = $name;

                    break;
                case AddressLevel::CITY:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->prepareString($mainRelationData['name']);

                    $address->setCityFiasId($fiasId);
                    $address->setCityKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setCityType($levelSpec->getShortName());
                    $address->setCityTypeFull($levelSpec->getName());

                    $address->setCity($name);
                    $address->setCityWithType($this->resolveWithTypeName($name, $levelSpec));

                    // учитываем переименование городов
                    $actualNaming = $name;
                    break;
                case AddressLevel::SETTLEMENT:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->prepareString($mainRelationData['name']);

                    $address->setSettlementFiasId($fiasId);
                    $address->setSettlementKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setSettlementType($levelSpec->getShortName());
                    $address->setSettlementTypeFull($levelSpec->getName());

                    $address->setSettlement($name);
                    $address->setSettlementWithType($this->resolveWithTypeName($name, $levelSpec));

                    // учитываем переименование поселений
                    $actualNaming = $name;
                    break;
                case AddressLevel::TERRITORY:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->prepareString($mainRelationData['name']);

                    $address->setTerritoryFiasId($fiasId);
                    $address->setTerritoryKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setTerritoryType($levelSpec->getShortName());
                    $address->setTerritoryTypeFull($levelSpec->getName());

                    $address->setTerritory($name);
                    $address->setTerritoryWithType($this->resolveWithTypeName($name, $levelSpec));

                    // учитываем переименование территорий
                    $actualNaming = $name;
                    break;
                case AddressLevel::STREET:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->prepareString($mainRelationData['name']);

                    $address->setStreetFiasId($fiasId);
                    $address->setStreetKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setStreetType($levelSpec->getShortName());
                    $address->setStreetTypeFull($levelSpec->getName());

                    $address->setStreet($name);
                    $address->setStreetWithType($this->resolveWithTypeName($name, $levelSpec));

                    // учитываем переименование улиц
                    $actualNaming = $name;
                    break;
                case AddressLevel::HOUSE:
                    $fiasId = $mainRelationData['objectguid'];
                    $address->setHouseFiasId($fiasId);
                    $address->setHouseKladrId($kladrId);

                    $levelSpec = $this->houseSpecResolver->resolve((int)$mainRelationData['housetype']);
                    $address->setHouseType($levelSpec->getShortName());
                    $address->setHouseTypeFull($levelSpec->getName());

                    $address->setHouse($this->prepareString($mainRelationData['housenum']));

                    $address->setBlock1($mainRelationData['addnum1'] ?? null);
                    if ($mainRelationData['addtype1']) {
                        $blockTypeName = $this->addHouseSpecResolver->resolve((int)$mainRelationData['addtype1']);
                        $address->setBlockType1($blockTypeName->getShortName());
                        $address->setBlockTypeFull1($blockTypeName->getName());
                    }

                    $address->setBlock2($mainRelationData['addnum2'] ?? null);
                    if ($mainRelationData['addtype2']) {
                        $blockTypeName = $this->addHouseSpecResolver->resolve((int)$mainRelationData['addtype2']);
                        $address->setBlockType2($blockTypeName->getShortName());
                        $address->setBlockTypeFull2($blockTypeName->getName());
                    }
                    break;
                case AddressLevel::FLAT:
                    $fiasId = $mainRelationData['objectguid'];
                    $address->setFlatFiasId($fiasId);

                    $levelSpec = $this->apartmentSpecResolver->resolve((int)$mainRelationData['aparttype']);
                    $address->setFlatType($levelSpec->getShortName());
                    $address->setFlatTypeFull($levelSpec->getName());

                    $address->setFlat($this->prepareString($mainRelationData['number']));
                    break;
                case AddressLevel::ROOM:
                    $fiasId = $mainRelationData['objectguid'];
                    $address->setRoomFiasId($fiasId);

                    $levelSpec = $this->roomSpecResolver->resolve((int)$mainRelationData['roomtype']);
                    $address->setRoomType($levelSpec->getShortName());
                    $address->setRoomTypeFull($levelSpec->getName());

                    $address->setRoom($this->prepareString($mainRelationData['number']));
                    break;
                case AddressLevel::STEAD:
                case AddressLevel::CAR_PLACE:
                    // эти уровни не индексируем, таким образом сюда они попадать не должны
                    throw new InvalidAddressLevelException(sprintf('Unsupported address level "%d".', $addressLevel));
            }

            // последний уровень данных
            // TODO: подумать о повторах для СНТ
            // как быть с тем что на этом последнем уровне может быть несколько relation
            if ($addressLevel === \array_key_last($groupedParents)) {
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

                // переименования и синонимы пишем только для определенных level на конечном уровне данных
                switch ($addressLevel) {
                    case AddressLevel::REGION:
                    case AddressLevel::AREA:
                    case AddressLevel::CITY:
                    case AddressLevel::SETTLEMENT:
                    case AddressLevel::STREET:
                        $address->setRenaming($this->resolveLevelRenaming($levelParents, $actualNaming));
                        $address->setSynonyms($this->addressSynonymizer->getSynonyms($fiasId));
                        break;
                }
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

        // TODO: проверить что используется только для последнего уровня
        // TODO: прибавлять level spec short
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
    private function resolveAddressLevel(array $relation): int
    {
        $relationType = $relation['relation_type'];

        switch ($relationType) {
            case FiasRelationType::ADDR_OBJ:
                $fiasLevel = (int)$relation['relation_data']['level'];

                return FiasLevel::mapAdmHierarchyToAddressLevel($fiasLevel);
            case FiasRelationType::HOUSE:
                return AddressLevel::HOUSE;
            case FiasRelationType::APARTMENT:
                return AddressLevel::FLAT;
            case FiasRelationType::ROOM:
                return AddressLevel::ROOM;
            case FiasRelationType::CAR_PLACE:
                return AddressLevel::CAR_PLACE;
            case FiasRelationType::STEAD:
                return AddressLevel::STEAD;
        }

        throw new RuntimeException(sprintf('Failed to resolve AddressLevel by relation_type "%s"', $relationType));
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

    private function resolveWithTypeName(string $name, AddressLevelSpec $addressLevelSpec): string
    {
        switch ($addressLevelSpec->getNamePosition()) {
            case AddressLevelSpec::NAME_POSITION_BEFORE:
                return $addressLevelSpec->getShortName().' '.$name;
            case AddressLevelSpec::NAME_POSITION_AFTER:
                return $name.' '.$addressLevelSpec->getShortName();
        }
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
