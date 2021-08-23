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
    private RelationLevelResolver $relationLevelResolver;

    public function __construct(
        ObjectAddressLevelSpecResolverInterface $addrObjectTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $houseTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $addHouseTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $apartmentTypeNameResolver,
        TypeAddressLevelSpecResolverInterface $roomTypeNameResolver,
        ActualityComparator $actualityPeriodComparator,
        AddressSynonymizer $addressSynonymizer,
        RelationLevelResolver $relationLevelResolver
    ) {
        $this->addrObjectSpecResolver = $addrObjectTypeNameResolver;
        $this->houseSpecResolver = $houseTypeNameResolver;
        $this->addHouseSpecResolver = $addHouseTypeNameResolver;
        $this->apartmentSpecResolver = $apartmentTypeNameResolver;
        $this->roomSpecResolver = $roomTypeNameResolver;
        $this->actualityPeriodComparator = $actualityPeriodComparator;
        $this->addressSynonymizer = $addressSynonymizer;
        $this->relationLevelResolver = $relationLevelResolver;
    }

    // todo: too huge loop
    public function build(array $data, ?Address $existsAddress = null): Address
    {
        $objectId = (int)$data['object_id'];
        $path = array_map('intval', explode('.', $data['path_ltree']));

        $objects = json_decode($data['objects'], true, 512, JSON_THROW_ON_ERROR);
        $params = json_decode($data['params'], true, 512, JSON_THROW_ON_ERROR);

//        /**
//         * Группируем по AddressLevel. Так как дополнительные локаций таких как СНТ, ГСК mapped на один и тот же
//         * уровень AddressLevel::SETTLEMENT может быть несколько актуальных значений.
//         *
//         * Мы бы могли разбить path_ltree на составные части и итерировать по ним, но и в этому случае остается проблема
//         * определения соотношения relation с полями Address.
//         */
//        $parentsByLevel = [];
//        foreach ($objects as $item) {
//            $relations = $item['relations'];
//            foreach ($relations as $relation) {
//                $addressLevel = $this->relationLevelResolver->resolveAddressLevel($relation);
//
//                $parentsByLevel[$addressLevel] = $parentsByLevel[$addressLevel] ?? [];
//                $parentsByLevel[$addressLevel][] = $relation;
//            }
//        }

        $relationsByObject = array_column($objects, 'relations', 'object_id');
        $paramsByObject = array_column($params, 'values', 'object_id');

        // мы должны сохранить изменения внесенные другими builder
        $address = $existsAddress ?? new Address();
        $actualName = null;

        $levelApplied = [];

        foreach ($path as $pathObjectId) {
            if (!isset($relationsByObject[$pathObjectId])) {
                throw AddressBuildFailedException::withObjectId(
                    'There are no relations for path.',
                    $pathObjectId,
                );
            }
            $objectRelations = $relationsByObject[$pathObjectId];

            // находим актуальное значение
            $actualObjectRelation = array_values(
                array_filter(
                    $objectRelations,
                    static function ($item) {
                        return $item['is_active'] && $item['is_actual'];
                    }
                )
            );

            $cnt = count($actualObjectRelation);
            if ($cnt !== 1) {
                throw AddressBuildFailedException::withObjectId(
                    sprintf('There are %d actual relations', $cnt),
                    $pathObjectId,
                );
            }

            $actualObjectRelation = $actualObjectRelation[0];

//            $mainRelation = null;
//
//            switch ($cnt) {
//                case 0:
//                    /**
//                     * Все relation на одном уровне AddressLevel неактивные.
//                     * Такое было ранее при группировке по FiasLevel (для устаревших уровней, например ADDITIONAL_TERRITORIES_LEVEL),
//                     * при группировке по AddressLevel - такого быть не должно поэтому бросаем exception.
//                     *
//                     * пример: г Казань, тер ГСК Монтажник - был перемещен по уровню ФИАС. Был ранее на уровне 8 (до 2019-05-05),
//                     * далее перемещен на 7. В итоге на 8 уровне у него нет актуальных relation.
//                     * Такие уровни мы должны пропускать.
//                     */
//                    continue 2; // 2 - because in switch
//                case 1:
//                    $mainRelation = $levelActualRelations[0];
//                    break;
//                default:
//                    /**
//                     * Есть несколько активных relations на одном уровне AddressLevel.
//                     *
//                     * Причиной может быть несколько вещей:
//                     *  1) несколько уровней ФИАС могут соответствовать одному нашему уровню.
//                     *  Для решения этой проблемы мы будем выбирать один главный relation.
//                     *  2) некоторых случаях гараж (погреб, подвал) могут быть заданы как внутри дома, так и
//                     *  в виде отдельного дома - с этиим не понятно как быть
//                     */
//                    $mainRelation = $this->mainLevelRelationResolver->resolve($addressLevel, $levelActualRelations);
//                    break;
//            }

            $actualRelationData = $actualObjectRelation['data'];

            $actualParams = $this->resolveActualParams(
                $paramsByObject[$pathObjectId] ?? [],
                [FiasParamType::KLADR, FiasParamType::OKATO, FiasParamType::OKTMO, FiasParamType::POSTAL_CODE]
            );

            $kladrId = $actualParams[FiasParamType::KLADR]['value'] ?? null;
            $okato = $actualParams[FiasParamType::OKATO]['value'] ?? null;
            $oktmo = $actualParams[FiasParamType::OKTMO]['value'] ?? null;
            $postalCode = $actualParams[FiasParamType::POSTAL_CODE]['value'] ?? null;

            $fiasId = null;
            $fiasLevel = $this->relationLevelResolver->resolveFiasLevel($actualObjectRelation);
            $addressLevel = $this->relationLevelResolver->resolveAddressLevel($actualObjectRelation);

            switch ($addressLevel) {
                case AddressLevel::REGION:
                    $fiasId = $actualRelationData['objectguid'];
                    if ('' === $fiasId) {
                        throw AddressBuildFailedException::withObjectId(
                            sprintf('Empty fiasId for region level.'),
                            $pathObjectId
                        );
                    }

                    $name = $this->emptyStrToNull($actualRelationData['name']);
                    if ('' === $name) {
                        throw AddressBuildFailedException::withObjectId(
                            sprintf('Empty name for region level.'),
                            $pathObjectId
                        );
                    }

                    $address->setRegionFiasId($fiasId);
                    $address->setRegionKladrId($kladrId);

                    $houseSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $actualRelationData['typename']);
                    $address->setRegionType($houseSpec->getShortName());
                    $address->setRegionTypeFull($houseSpec->getName());

                    $address->setRegion($name);
                    $address->setRegionWithType($this->resolveWithShortTypeName($name, $houseSpec));
                    $address->setRegionWithFullType($this->resolveWithFullTypeName($name, $houseSpec));

                    // учитываем переименование регионов
                    $actualName = $name;
                    break;
                case AddressLevel::AREA:
                    $fiasId = $actualRelationData['objectguid'];
                    $name = $this->emptyStrToNull($actualRelationData['name']);

                    $address->setAreaFiasId($fiasId);
                    $address->setAreaKladrId($kladrId);

                    $houseSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $actualRelationData['typename']);
                    $address->setAreaType($houseSpec->getShortName());
                    $address->setAreaTypeFull($houseSpec->getName());

                    $address->setArea($name);
                    $address->setAreaWithType($this->resolveWithShortTypeName($name, $houseSpec));
                    $address->setAreaWithFullType($this->resolveWithFullTypeName($name, $houseSpec));

                    // учитываем переименование районов
                    $actualName = $name;

                    break;
                case AddressLevel::CITY:
                    $fiasId = $actualRelationData['objectguid'];
                    $name = $this->emptyStrToNull($actualRelationData['name']);

                    $address->setCityFiasId($fiasId);
                    $address->setCityKladrId($kladrId);

                    $houseSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $actualRelationData['typename']);
                    $address->setCityType($houseSpec->getShortName());
                    $address->setCityTypeFull($houseSpec->getName());

                    $address->setCity($name);
                    $address->setCityWithType($this->resolveWithShortTypeName($name, $houseSpec));
                    $address->setCityWithFullType($this->resolveWithFullTypeName($name, $houseSpec));

                    // учитываем переименование городов
                    $actualName = $name;
                    break;
                case AddressLevel::SETTLEMENT:
                    $fiasId = $actualRelationData['objectguid'];
                    $name = $this->emptyStrToNull($actualRelationData['name']);

                    $address->setSettlementFiasId($fiasId);
                    $address->setSettlementKladrId($kladrId);

                    $houseSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $actualRelationData['typename']);
                    $address->setSettlementType($houseSpec->getShortName());
                    $address->setSettlementTypeFull($houseSpec->getName());

                    $address->setSettlement($name);
                    $address->setSettlementWithType($this->resolveWithShortTypeName($name, $houseSpec));
                    $address->setSettlementWithFullType($this->resolveWithFullTypeName($name, $houseSpec));

                    // учитываем переименование поселений
                    $actualName = $name;
                    break;
                case AddressLevel::TERRITORY:
                    $fiasId = $actualRelationData['objectguid'];
                    $name = $this->emptyStrToNull($actualRelationData['name']);

                    $address->setTerritoryFiasId($fiasId);
                    $address->setTerritoryKladrId($kladrId);

                    $houseSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $actualRelationData['typename']);
                    $address->setTerritoryType($houseSpec->getShortName());
                    $address->setTerritoryTypeFull($houseSpec->getName());

                    $address->setTerritory($name);
                    $address->setTerritoryWithType($this->resolveWithShortTypeName($name, $houseSpec));
                    $address->setTerritoryWithFullType($this->resolveWithFullTypeName($name, $houseSpec));

                    // учитываем переименование территорий
                    $actualName = $name;
                    break;
                case AddressLevel::STREET:
                    $fiasId = $actualRelationData['objectguid'];
                    $name = $this->emptyStrToNull($actualRelationData['name']);

                    $address->setStreetFiasId($fiasId);
                    $address->setStreetKladrId($kladrId);

                    $houseSpec = $this->addrObjectSpecResolver->resolve($fiasLevel, $actualRelationData['typename']);
                    $address->setStreetType($houseSpec->getShortName());
                    $address->setStreetTypeFull($houseSpec->getName());

                    $address->setStreet($name);
                    $address->setStreetWithType($this->resolveWithShortTypeName($name, $houseSpec));
                    $address->setStreetWithFullType($this->resolveWithFullTypeName($name, $houseSpec));

                    // учитываем переименование улиц
                    $actualName = $name;
                    break;
                case AddressLevel::HOUSE:
                    $fiasId = $actualRelationData['objectguid'];
                    $houseNum = $this->emptyStrToNull($actualRelationData['housenum']);

                    $houseSpec = null;
                    if (null !== $houseNum) {
                        if (0 === (int)$actualRelationData['housetype']) {
                            throw EmptyLevelTypeException::withObjectId('housetype', $pathObjectId);
                        }
                        $houseSpec = $this->houseSpecResolver->resolve((int)$actualRelationData['housetype']);
                    }

                    $addNum1 = $this->emptyStrToNull($actualRelationData['addnum1']);
                    $block1Spec = null;
                    if (null !== $addNum1) {
                        if (0 === (int)$actualRelationData['addtype1']) {
                            throw EmptyLevelTypeException::withObjectId('addtype1', $pathObjectId);
                        }

                        $block1Spec = $this->addHouseSpecResolver->resolve((int)$actualRelationData['addtype1']);
                    }

                    $addNum2 = $this->emptyStrToNull($actualRelationData['addnum2']);
                    $block2Spec = null;
                    if (null !== $addNum2) {
                        if (0 === (int)$actualRelationData['addtype2']) {
                            throw EmptyLevelTypeException::withObjectId('addtype2', $pathObjectId);
                        }

                        $block2Spec = $this->addHouseSpecResolver->resolve((int)$actualRelationData['addtype2']);
                    }

                    switch ($levelApplied[$addressLevel] ?? 0) {
                        case 0:
                            $address->setHouseFiasId($fiasId);
                            $address->setHouseKladrId($kladrId);

                            $address->setHouse($houseNum);
                            if (null !== $houseSpec) {
                                $address->setHouseType($houseSpec->getShortName());
                                $address->setHouseTypeFull($houseSpec->getName());
                            }

                            $address->setBlock1($addNum1);
                            if (null !== $block1Spec) {
                                $address->setBlockType1($block1Spec->getShortName());
                                $address->setBlockTypeFull1($block1Spec->getName());
                            }

                            $address->setBlock2($addNum2);
                            if (null !== $block2Spec) {
                                $address->setBlockType2($block2Spec->getShortName());
                                $address->setBlockTypeFull2($block2Spec->getName());
                            }
                            break;
                        case 1:
                            $this->setFlatLevelData(
                                $address,
                                $fiasId,
                                $houseNum,
                                $houseSpec->getShortName(),
                                $houseSpec->getName()
                            );
                            break;
                        default:
                            throw AddressBuildFailedException::withObjectId(
                                sprintf('There are more than 2 actual relation on one level %d.', $addressLevel),
                                $pathObjectId,
                            );
                    }
                    break;
                case AddressLevel::FLAT:
                    $fiasId = $actualRelationData['objectguid'];

                    $apartmentType = (int)$actualRelationData['aparttype'];

                    /**
                     * В БД присутствует значение 0. Считаем что это квартира.
                     * @see 2320587
                     */
                    if (0 === $apartmentType) {
                        $apartmentType = 2;
                    }
                    $houseSpec = $this->apartmentSpecResolver->resolve($apartmentType);

                    $this->setFlatLevelData(
                        $address,
                        $fiasId,
                        $actualRelationData['number'],
                        $houseSpec->getShortName(),
                        $houseSpec->getName()
                    );
                    break;
                case AddressLevel::ROOM:
                    $fiasId = $actualRelationData['objectguid'];
                    $address->setRoomFiasId($fiasId);

                    $houseSpec = $this->roomSpecResolver->resolve((int)$actualRelationData['roomtype']);
                    $address->setRoomType($houseSpec->getShortName());
                    $address->setRoomTypeFull($houseSpec->getName());

                    $address->setRoom($this->emptyStrToNull($actualRelationData['number']));
                    break;
                case AddressLevel::STEAD:
                case AddressLevel::CAR_PLACE:
                    // эти уровни не индексируем, таким образом сюда они попадать не должны
                    throw new InvalidAddressLevelException(sprintf('Unsupported address level "%d".', $addressLevel));
            }

            $levelApplied[$addressLevel] = $levelApplied[$addressLevel] ?? 0;
            $levelApplied[$addressLevel]++;

            // последний уровень данных
            if ($pathObjectId === $objectId) {
                Assert::notNull($fiasId, 'Empty fiasId');

                $address->setFiasId($fiasId);
                $address->setAddressLevel($addressLevel);
                $address->setFiasLevel($fiasLevel);
                $address->setFiasObjectId($pathObjectId);
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
                        $address->setRenaming($this->resolveLevelRenaming($objectRelations, $actualName) ?: null);
                        $address->setSynonyms($this->addressSynonymizer->getSynonyms($fiasId) ?: null);
                        break;
                }
            }
        }

        return $address;
    }

    private function setFlatLevelData(
        Address $address,
        string $fiasId,
        ?string $number,
        string $type,
        string $typeFull
    ): void {
        $address->setFlatFiasId($fiasId);
        $address->setFlat($this->emptyStrToNull($number));

        $address->setFlatType($type);
        $address->setFlatTypeFull($typeFull);
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
