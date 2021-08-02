<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\ActualityComparator;
use Addresser\AddressRepository\Address;
use Addresser\AddressRepository\AddressBuilderInterface;
use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressSynonymizer;
use Addresser\AddressRepository\Exceptions\AddressBuildFailedException;
use Addresser\AddressRepository\Fias\LevelNameResolvers\FiasObjectLevelNameResolver;
use Addresser\AddressRepository\LevelName;
use Addresser\AddressRepository\LevelNameNormalizer;

/**
 * Формирует адрес на основе данных из ФИАС.
 * Работает только со структурой которую возвращает Finder.
 */
class FiasAddressBuilder implements AddressBuilderInterface
{
    private FiasObjectLevelNameResolver $addrObjectTypeNameResolver;
    private FiasLevelNameResolverInterface $houseTypeNameResolver;
    private FiasLevelNameResolverInterface $addHouseTypeNameResolver;
    private FiasLevelNameResolverInterface $apartmentTypeNameResolver;
    private FiasLevelNameResolverInterface $roomTypeNameResolver;
    private LevelNameNormalizer $levelNameNormalizer;
    private ActualityComparator $actualityPeriodComparator;
    private AddressSynonymizer $addressSynonymizer;

    public function __construct(
        FiasObjectLevelNameResolver $addrObjectTypeNameResolver,
        FiasLevelNameResolverInterface $houseTypeNameResolver,
        FiasLevelNameResolverInterface $addHouseTypeNameResolver,
        FiasLevelNameResolverInterface $apartmentTypeNameResolver,
        FiasLevelNameResolverInterface $roomTypeNameResolver,
        LevelNameNormalizer $levelNameNormalizer,
        ActualityComparator $actualityPeriodComparator,
        AddressSynonymizer $addressSynonymizer
    )
    {
        $this->addrObjectTypeNameResolver = $addrObjectTypeNameResolver;
        $this->houseTypeNameResolver = $houseTypeNameResolver;
        $this->addHouseTypeNameResolver = $addHouseTypeNameResolver;
        $this->apartmentTypeNameResolver = $apartmentTypeNameResolver;
        $this->roomTypeNameResolver = $roomTypeNameResolver;
        $this->levelNameNormalizer = $levelNameNormalizer;
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
                $actualItem['params'],
                [FiasParamType::KLADR, FiasParamType::OKATO, FiasParamType::OKTMO, FiasParamType::POSTAL_CODE]
            );

            $kladrId = $actualParams[FiasParamType::KLADR]['value'] ?? null;
            $okato = $actualParams[FiasParamType::OKATO]['value'] ?? null;
            $oktmo = $actualParams[FiasParamType::OKTMO]['value'] ?? null;
            $postalCode = $actualParams[FiasParamType::POSTAL_CODE]['value'] ?? null;

            $typeName = $this->getNormalizedLevelName($addressLevel, $fiasLevel, $relationData);

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
                    $address->setRegionType($typeName->getShortName());
                    $address->setRegionTypeFull($typeName->getName());
                    $address->setRegion($this->notEmptyString($name));
                    // учитываем переименование регионов
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::AREA:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setAreaFiasId($fiasId);
                    $address->setAreaKladrId($kladrId);
                    $address->setAreaType($typeName->getShortName());
                    $address->setAreaTypeFull($typeName->getName());
                    $address->setArea($this->notEmptyString($name));
                    // учитываем переименование районов
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::CITY:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setCityFiasId($fiasId);
                    $address->setCityKladrId($kladrId);
                    $address->setCityType($typeName->getShortName());
                    $address->setCityTypeFull($typeName->getName());
                    $address->setCity($this->notEmptyString($name));
                    // учитываем переименование городов
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::SETTLEMENT:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setSettlementFiasId($fiasId);
                    $address->setSettlementKladrId($kladrId);
                    $address->setSettlementType($typeName->getShortName());
                    $address->setSettlementTypeFull($typeName->getName());
                    $address->setSettlement($this->notEmptyString($name));
                    // учитываем переименование поселений
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::STREET:
                    $fiasId = $relationData['objectguid'];
                    $name = $relationData['name'];

                    $address->setStreetFiasId($fiasId);
                    $address->setStreetKladrId($kladrId);
                    $address->setStreetType($typeName->getShortName());
                    $address->setStreetTypeFull($typeName->getName());
                    $address->setStreet($this->notEmptyString($name));
                    // учитываем переименование улиц
                    $address->setRenaming($this->resolveLevelRenaming($levelItems, $name));
                    break;
                case AddressLevel::HOUSE:
                    $fiasId = $relationData['objectguid'];
                    $address->setHouseFiasId($fiasId);
                    $address->setHouseKladrId($kladrId);
                    $address->setHouseType($typeName->getShortName());
                    $address->setHouseTypeFull($typeName->getName());
                    $address->setHouse($this->notEmptyString($relationData['housenum']));

                    $address->setBlock1($relationData['addnum1'] ?? null);
                    if ($relationData['addtype1']) {
                        $blockTypeName = $this->getNormalizedHouseBlockTypeName((int)$relationData['addtype1']);

                        $address->setBlockType1($blockTypeName->getShortName());
                        $address->setBlockTypeFull1($blockTypeName->getName());
                    }

                    $address->setBlock2($relationData['addnum2'] ?? null);
                    if ($relationData['addtype2']) {
                        $blockTypeName = $this->getNormalizedHouseBlockTypeName((int)$relationData['addtype2']);

                        $address->setBlockType2($blockTypeName->getShortName());
                        $address->setBlockTypeFull2($blockTypeName->getName());
                    }
                    break;
                case AddressLevel::FLAT:
                    $fiasId = $relationData['objectguid'];
                    $address->setFlatFiasId($fiasId);
                    $address->setFlatType($typeName->getShortName());
                    $address->setFlatTypeFull($typeName->getName());
                    $address->setFlat($this->notEmptyString($relationData['number']));
                    break;
                case AddressLevel::ROOM:
                    $fiasId = $relationData['objectguid'];
                    $address->setRoomFiasId($fiasId);
                    $address->setRoomType($typeName->getShortName());
                    $address->setRoomTypeFull($typeName->getName());
                    $address->setRoom($this->notEmptyString($relationData['number']));
                    break;
            }

            // данные последнего уровня
            if ($addressLevel === array_key_last($parentsByLevels)) {
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

    private function getNormalizedLevelName(int $addressLevel, int $fiasLevel, array $relationData): LevelName
    {
        $typeName = null;

        switch ($addressLevel) {
            case AddressLevel::REGION:
            case AddressLevel::AREA:
            case AddressLevel::CITY:
            case AddressLevel::SETTLEMENT:
            case AddressLevel::STREET:
                $typeName = $this->addrObjectTypeNameResolver->resolve($fiasLevel, $relationData['typename']);
                break;
            case AddressLevel::HOUSE:
                // Респ Башкортостан, г Кумертау, ул Брикетная, влд 5 к А стр 1/6
                $typeName = $this->houseTypeNameResolver->resolve((int)$relationData['housetype']);
                break;
            case AddressLevel::FLAT:
                $typeName = $this->apartmentTypeNameResolver->resolve((int)$relationData['aparttype']);
                break;
            case AddressLevel::ROOM:
                $typeName = $this->roomTypeNameResolver->resolve((int)$relationData['roomtype']);
                break;
        }

        if (null === $typeName) {
            throw new AddressBuildFailedException(
                sprintf('Failed to find typeName for level %d', $addressLevel)
            );
        }

        return $this->levelNameNormalizer->normalize($typeName);
    }

    private function getNormalizedHouseBlockTypeName(int $addType): LevelName
    {
        return $this->levelNameNormalizer->normalize($this->addHouseTypeNameResolver->resolve($addType));
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

    private function notEmptyString(?string $s): ?string
    {
        if (null === $s) {
            return null;
        }
        $tmp = trim($s);

        return empty($tmp) ? null : $tmp;
    }
}
