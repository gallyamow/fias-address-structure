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
use Addresser\AddressRepository\Exceptions\EmptyLevelTypeException;
use Addresser\AddressRepository\Exceptions\InvalidAddressLevelException;
use Addresser\AddressRepository\Exceptions\RuntimeException;
use Webmozart\Assert\Assert;

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
    private MainRelationResolver $mainLevelRelationResolver;
    private RelationLevelResolver $relationLevelResolver;

    public function __construct(
        ObjectAddressLevelSpecResolverInterface $addrObjectTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $houseTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $addHouseTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $apartmentTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $roomTypeNameResolver,
        ActualityComparator $actualityPeriodComparator,
        AddressSynonymizer $addressSynonymizer,
        MainRelationResolver $mainLevelRelationResolver,
        RelationLevelResolver $relationLevelResolver
    ) {
        $this->addrObjectSpecResolver = $addrObjectTypeNameResolver;
        $this->houseSpecResolver = $houseTypeNameResolver;
        $this->addHouseSpecResolver = $addHouseTypeNameResolver;
        $this->apartmentSpecResolver = $apartmentTypeNameResolver;
        $this->roomSpecResolver = $roomTypeNameResolver;
        $this->actualityPeriodComparator = $actualityPeriodComparator;
        $this->addressSynonymizer = $addressSynonymizer;
        $this->mainLevelRelationResolver = $mainLevelRelationResolver;
        $this->relationLevelResolver = $relationLevelResolver;
    }

    // todo: too huge loop
    public function build(array $data, ?Address $existsAddress = null): Address
    {
        $objectId = (int)$data['object_id'];

        $objects = json_decode($data['objects'], true, 512, JSON_THROW_ON_ERROR);
        $params = json_decode($data['params'], true, 512, JSON_THROW_ON_ERROR);

        /**
         * Группируем по AddressLevel. Так как дополнительные локаций таких как СНТ, ГСК mapped на один и тот же
         * уровень AddressLevel::SETTLEMENT может быть несколько актуальных значений.
         */
        $parentsByLevel = [];
        foreach ($objects as $item) {
            $relations = $item['relations'];
            foreach ($relations as $relation) {
                $addressLevel = $this->relationLevelResolver->resolveAddressLevel($relation);

                $parentsByLevel[$addressLevel] = $parentsByLevel[$addressLevel] ?? [];
                $parentsByLevel[$addressLevel][] = $relation;
            }
        }

        $paramsByObject = [];
        foreach ($params as $item) {
            $paramsByObject[$item['object_id']] = $item['values'];
        }

        // мы должны сохранить изменения внесенные другими builder
        $address = $existsAddress ?? new Address();
        $actualName = null;

        foreach ($parentsByLevel as $addressLevel => $levelRelations) {
            // находим актуальное значение
            $actualRelations = array_values(
                array_filter(
                    $levelRelations,
                    static function ($item) {
                        return $item['is_active'] && $item['is_actual'];
                    }
                )
            );

            $mainRelation = null;

            $cnt = count($actualRelations);
            switch ($cnt) {
                case 0:
                    /**
                     * Все relation на одном уровне AddressLevel неактивные.
                     * Такое было ранее при группировке по FiasLevel (для устаревших уровней, например ADDITIONAL_TERRITORIES_LEVEL),
                     * при группировке по AddressLevel - такого быть не должно поэтому бросаем exception.
                     *
                     * пример: г Казань, тер ГСК Монтажник - был перемещен по уровню ФИАС. Был ранее на уровне 8 (до 2019-05-05),
                     * далее перемещен на 7. В итоге на 8 уровне у него нет актуальных relation.
                     * Такие уровни мы должны пропускать.
                     */
                    continue 2; // 2 - because in switch
                case 1:
                    $mainRelation = $actualRelations[0];
                    break;
                default:
                    /**
                     * Есть несколько активных relations на одном уровне AddressLevel. Проблема кроется в том что
                     * несколько уровней ФИАС могут соответствовать одному нашему уровню.
                     * Для решения этой проблемы мы будем выбирать один главный relation.
                     */
                    $mainRelation = $this->mainLevelRelationResolver->resolve($addressLevel, $actualRelations);
                    break;
            }

            $mainRelationData = $mainRelation['data'];

            //  лучше бы использовать object_id из уровня выше
            $mainRelationObjectId = (int)$mainRelationData['objectid'];

            $fiasLevel = $this->relationLevelResolver->resolveFiasLevel($mainRelation);

            $actualParams = $this->resolveActualParams(
                $paramsByObject[$mainRelationObjectId] ?? [],
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
                    if ('' === $fiasId) {
                        throw AddressBuildFailedException::withObjectId(
                            sprintf('Empty fiasId for region level.'),
                            $objectId
                        );
                    }

                    $name = $this->emptyStrToNull($mainRelationData['name']);
                    if ('' === $name) {
                        throw AddressBuildFailedException::withObjectId(
                            sprintf('Empty name for region level.'),
                            $objectId
                        );
                    }

                    $address->setRegionFiasId($fiasId);
                    $address->setRegionKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setRegionType($levelSpec->getShortName());
                    $address->setRegionTypeFull($levelSpec->getName());

                    $address->setRegion($name);
                    $address->setRegionWithType($this->resolveWithShortTypeName($name, $levelSpec));
                    $address->setRegionWithFullType($this->resolveWithFullTypeName($name, $levelSpec));

                    // учитываем переименование регионов
                    $actualName = $name;
                    break;
                case AddressLevel::AREA:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->emptyStrToNull($mainRelationData['name']);

                    $address->setAreaFiasId($fiasId);
                    $address->setAreaKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setAreaType($levelSpec->getShortName());
                    $address->setAreaTypeFull($levelSpec->getName());

                    $address->setArea($name);
                    $address->setAreaWithType($this->resolveWithShortTypeName($name, $levelSpec));
                    $address->setAreaWithFullType($this->resolveWithFullTypeName($name, $levelSpec));

                    // учитываем переименование районов
                    $actualName = $name;

                    break;
                case AddressLevel::CITY:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->emptyStrToNull($mainRelationData['name']);

                    $address->setCityFiasId($fiasId);
                    $address->setCityKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setCityType($levelSpec->getShortName());
                    $address->setCityTypeFull($levelSpec->getName());

                    $address->setCity($name);
                    $address->setCityWithType($this->resolveWithShortTypeName($name, $levelSpec));
                    $address->setCityWithFullType($this->resolveWithFullTypeName($name, $levelSpec));

                    // учитываем переименование городов
                    $actualName = $name;
                    break;
                case AddressLevel::SETTLEMENT:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->emptyStrToNull($mainRelationData['name']);

                    $address->setSettlementFiasId($fiasId);
                    $address->setSettlementKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setSettlementType($levelSpec->getShortName());
                    $address->setSettlementTypeFull($levelSpec->getName());

                    $address->setSettlement($name);
                    $address->setSettlementWithType($this->resolveWithShortTypeName($name, $levelSpec));
                    $address->setSettlementWithFullType($this->resolveWithFullTypeName($name, $levelSpec));

                    // учитываем переименование поселений
                    $actualName = $name;
                    break;
                case AddressLevel::TERRITORY:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->emptyStrToNull($mainRelationData['name']);

                    $address->setTerritoryFiasId($fiasId);
                    $address->setTerritoryKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setTerritoryType($levelSpec->getShortName());
                    $address->setTerritoryTypeFull($levelSpec->getName());

                    $address->setTerritory($name);
                    $address->setTerritoryWithType($this->resolveWithShortTypeName($name, $levelSpec));
                    $address->setTerritoryWithFullType($this->resolveWithFullTypeName($name, $levelSpec));

                    // учитываем переименование территорий
                    $actualName = $name;
                    break;
                case AddressLevel::STREET:
                    $fiasId = $mainRelationData['objectguid'];
                    $name = $this->emptyStrToNull($mainRelationData['name']);

                    $address->setStreetFiasId($fiasId);
                    $address->setStreetKladrId($kladrId);

                    $levelSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $mainRelationData['typename']);
                    $address->setStreetType($levelSpec->getShortName());
                    $address->setStreetTypeFull($levelSpec->getName());

                    $address->setStreet($name);
                    $address->setStreetWithType($this->resolveWithShortTypeName($name, $levelSpec));
                    $address->setStreetWithFullType($this->resolveWithFullTypeName($name, $levelSpec));

                    // учитываем переименование улиц
                    $actualName = $name;
                    break;
                case AddressLevel::HOUSE:
                    $fiasId = $mainRelationData['objectguid'];
                    $address->setHouseFiasId($fiasId);
                    $address->setHouseKladrId($kladrId);

                    $tmp = $this->emptyStrToNull($mainRelationData['housenum']);
                    $address->setHouse($tmp);
                    if (null !== $tmp) {
                        $type = (int)$mainRelationData['housetype'];
                        if (0 === $type) {
                            throw EmptyLevelTypeException::withObjectId('housetype', $objectId);
                        }

                        $levelSpec = $this->houseSpecResolver->resolve($type);
                        $address->setHouseType($levelSpec->getShortName());
                        $address->setHouseTypeFull($levelSpec->getName());
                    }

                    $tmp = $this->emptyStrToNull($mainRelationData['addnum1']);
                    $address->setBlock1($tmp);
                    if (null !== $tmp) {
                        $type = (int)$mainRelationData['addtype1'];
                        if (0 === $type) {
                            throw EmptyLevelTypeException::withObjectId('addtype1', $objectId);
                        }

                        $blockTypeName = $this->addHouseSpecResolver->resolve($type);
                        $address->setBlockType1($blockTypeName->getShortName());
                        $address->setBlockTypeFull1($blockTypeName->getName());
                    }

                    $tmp = $this->emptyStrToNull($mainRelationData['addnum2']);
                    $address->setBlock2($tmp);
                    if (null !== $tmp) {
                        $type = (int)$mainRelationData['addtype2'];
                        if (0 === $type) {
                            throw EmptyLevelTypeException::withObjectId('addtype2', $objectId);
                        }

                        $blockTypeName = $this->addHouseSpecResolver->resolve($type);
                        $address->setBlockType2($blockTypeName->getShortName());
                        $address->setBlockTypeFull2($blockTypeName->getName());
                    }
                    break;
                case AddressLevel::FLAT:
                    $fiasId = $mainRelationData['objectguid'];
                    $address->setFlatFiasId($fiasId);

                    $apartmentType = (int)$mainRelationData['aparttype'];

                    /**
                     * Бывает значение 0. Считаем что это квартира.
                     * @see 2320587
                     */
                    if (0 === $apartmentType) {
                        $apartmentType = 2;
                    }

                    $levelSpec = $this->apartmentSpecResolver->resolve($apartmentType);
                    $address->setFlatType($levelSpec->getShortName());
                    $address->setFlatTypeFull($levelSpec->getName());

                    $address->setFlat($this->emptyStrToNull($mainRelationData['number']));
                    break;
                case AddressLevel::ROOM:
                    $fiasId = $mainRelationData['objectguid'];
                    $address->setRoomFiasId($fiasId);

                    $levelSpec = $this->roomSpecResolver->resolve((int)$mainRelationData['roomtype']);
                    $address->setRoomType($levelSpec->getShortName());
                    $address->setRoomTypeFull($levelSpec->getName());

                    $address->setRoom($this->emptyStrToNull($mainRelationData['number']));
                    break;
                case AddressLevel::STEAD:
                case AddressLevel::CAR_PLACE:
                    // эти уровни не индексируем, таким образом сюда они попадать не должны
                    throw new InvalidAddressLevelException(sprintf('Unsupported address level "%d".', $addressLevel));
            }

            // последний уровень данных
            // if ($addressLevel === \array_key_last($parentsByLevel)) {
            if ($objectId === $mainRelationObjectId) {
                Assert::notNull($fiasId, 'Empty fiasId');

                $address->setFiasId($fiasId);
                $address->setAddressLevel($addressLevel);
                $address->setFiasLevel($fiasLevel);
                $address->setFiasObjectId($objectId);
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
                        $address->setRenaming($this->resolveLevelRenaming($levelRelations, $actualName) ?: null);
                        $address->setSynonyms($this->addressSynonymizer->getSynonyms($fiasId) ?: null);
                        break;
                }
            }
        }

        return $address;
    }

    private function resolveLevelRenaming(array $levelRelations, string $actualName, string $nameField = 'name'): array
    {
        $notActualRelations = array_values(
            array_filter(
                $levelRelations,
                static function ($item) {
                    return !($item['is_active'] && $item['is_actual']);
                }
            )
        );

        return array_values(
            array_filter(
                array_unique(
                    array_map(
                        static function ($item) use ($nameField) {
                            // здесь хорошо использовать withTypeName, но тогда будут проблемы в случае если
                            // у населенного пункта было и переименования и смета вида.
                            // то есть если сейчас г. Янаул, а в истории д. Янаул, с. Янаул = бывш. д.Янаул, с.Янаул
                            return $item['data'][$nameField];
                        },
                        $notActualRelations
                    )
                ),
                static function ($name) use ($actualName) {
                    return $name !== $actualName;
                }
            )
        );
    }

    private function resolveActualParams(array $objectParams, array $keys): array
    {
        $res = [];
        $currentDate = date('Y-m-d');

        foreach ($objectParams as $item) {
            $typeId = $item['type_id'];

            if (in_array($typeId, $keys, true)) {
                // сразу пропускаем неактуальные
                if ($item['end_date'] < $currentDate) {
                    continue;
                }

                $oldValueItem = $res[$typeId] ?? null;

                if (null === $oldValueItem
                    || ($oldValueItem && $this->actualityPeriodComparator->compare(
                            $oldValueItem['start_date'],
                            $oldValueItem['end_date'],
                            $item['start_date'],
                            $item['end_date']
                        ) === -1)
                ) {
                    // обновляем только если новое значении более актуальное чем старое
                    $res[$typeId] = $item;
                }
            }
        }

        return $res;
    }

    private function resolveWithFullTypeName(string $name, AddressLevelSpec $addressLevelSpec): string
    {
        return $this->buildWithTypeName($addressLevelSpec->getName(), $name, $addressLevelSpec->getNamePosition());
    }

    private function resolveWithShortTypeName(string $name, AddressLevelSpec $addressLevelSpec): string
    {
        return $this->buildWithTypeName($addressLevelSpec->getShortName(), $name, $addressLevelSpec->getNamePosition());
    }

    private function buildWithTypeName(string $typeName, string $name, int $position): string
    {
        switch ($position) {
            case AddressLevelSpec::NAME_POSITION_BEFORE:
                return $typeName.' '.$name;
            case AddressLevelSpec::NAME_POSITION_AFTER:
                return $name.' '.$typeName;
        }
    }

    private function emptyStrToNull(?string $s): ?string
    {
        if (null === $s) {
            return null;
        }
        $tmp = trim($s);

        return '' === $tmp ? null : $tmp;
    }
}
