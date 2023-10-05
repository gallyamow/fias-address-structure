<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure;

use Addresser\FiasAddressStructure\Exceptions\InvalidAddressLevelException;
use Webmozart\Assert\Assert;

/**
 * Плоская структура для хранения адреса.
 *
 * Одна и та же структура будет использована как для хранения информации до районов, так и для хранения
 * информации до квартир.
 *
 * Здесь присутствует некоторая денормализация:
 *  поля предыдущих уровней - чтобы всегда был доступ ко всем верхним уровням
 *  поля *withType, *withFullType - для упрощения работы клиентов
 *  поля *type, *typeFull - как справочная информация
 *
 * Для уровней ниже street полей withType - нет, потому что у них всегда тип стоит в NAME_POSITION_BEFORE.
 */
class Address implements \JsonSerializable, ArraySerializableInterface
{
    /**
     * ФИАС-код адреса (уровня).
     */
    private string $fiasId;

    /**
     * Это поле не относится к адресу и нужно для возобновления индексации.
     */
    private int $fiasObjectId;

    /**
     * @see FiasLevel
     */
    private int $fiasLevel;

    /**
     * @see AddressLevel
     */
    private int $addressLevel;

    private ?string $kladrId = null;
    private ?string $okato = null;
    private ?string $oktmo = null;
    private ?string $postalCode = null;

    /**
     * Поля региона.
     */
    private string $regionFiasId;
    private ?string $regionKladrId;
    private string $regionType;
    private string $regionTypeFull;
    private string $region;
    private string $regionWithType;
    private string $regionWithFullType;

    /**
     * Поля района внутри региона.
     */
    private ?string $areaFiasId = null;
    private ?string $areaKladrId = null;
    private ?string $areaType = null;
    private ?string $areaTypeFull = null;
    private ?string $area = null;
    private ?string $areaWithType = null;
    private ?string $areaWithFullType = null;

    /**
     * Поля города.
     */
    private ?string $cityFiasId = null;
    private ?string $cityKladrId = null;
    private ?string $cityType = null;
    private ?string $cityTypeFull = null;
    private ?string $city = null;
    private ?string $cityWithType = null;
    private ?string $cityWithFullType = null;

    /**
     * Поля поселения (внутри города или района).
     * Сюда могут попадать различные небольшие населенные пункты как внутри городов, так и внутри районов.
     */
    private ?string $settlementFiasId = null;
    private ?string $settlementKladrId = null;
    private ?string $settlementType = null;
    private ?string $settlementTypeFull = null;
    private ?string $settlement = null;
    private ?string $settlementWithType = null;
    private ?string $settlementWithFullType = null;

    /**
     * Поля территории (внутри города или района).
     * Сюда попадают СНТ, тер гк, зоны, гск.
     */
    private ?string $territoryFiasId = null;
    private ?string $territoryKladrId = null;
    private ?string $territoryType = null;
    private ?string $territoryTypeFull = null;
    private ?string $territory = null;
    private ?string $territoryWithType = null;
    private ?string $territoryWithFullType = null;

    /**
     * Поля улицы.
     */
    private ?string $streetFiasId = null;
    private ?string $streetKladrId = null;
    private ?string $streetType = null;
    private ?string $streetTypeFull = null;
    private ?string $street = null;
    private ?string $streetWithType = null;
    private ?string $streetWithFullType = null;

    /**
     * Поля дома.
     */
    private ?string $houseFiasId = null;
    private ?string $houseKladrId = null;
    private ?string $houseType = null;
    private ?string $houseTypeFull = null;
    private ?string $house = null;

    // корпус/строение (первая часть)
    private ?string $blockType1 = null;
    private ?string $blockTypeFull1 = null;
    private ?string $block1 = null;

    // корпус/строение (вторая часть)
    private ?string $blockType2 = null;
    private ?string $blockTypeFull2 = null;
    private ?string $block2 = null;

    /**
     * Поля квартиры.
     */
    private ?string $flatFiasId = null;
    private ?string $flatType = null;
    private ?string $flatTypeFull = null;
    private ?string $flat = null;

    /**
     * Поля помещения.
     */
    private ?string $roomFiasId = null;
    private ?string $roomType = null;
    private ?string $roomTypeFull = null;
    private ?string $room = null;

    /**
     * Синонимы - различные сокращенные названия вида Башкирия, Татария, ХМАО.
     */
    private ?array $synonyms = null;

    /**
     * Старые названия - переименования регионов, городов, улиц.
     * Заполняться будет только на необходимом уровне. Для дочерних - не будет.
     */
    private ?array $renaming = null;

    /**
     * Геопозиция.
     */
    private ?array $location = null;

    /**
     * Версия дельты (служебное поле).
     * @var int
     */
    private int $deltaVersion = 0;

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public static function fromArray(array $array): ArraySerializableInterface
    {
        $res = new self();
        $res->setFiasId($array['fiasId']);
        $res->setFiasObjectId($array['fiasObjectId']);
        $res->setFiasLevel($array['fiasLevel']);
        $res->setAddressLevel($array['addressLevel']);
        $res->setKladrId($array['kladrId']);
        $res->setOkato($array['okato']);
        $res->setOktmo($array['oktmo']);
        $res->setPostalCode($array['postalCode']);
        $res->setRegionFiasId($array['regionFiasId']);
        $res->setRegionKladrId($array['regionKladrId']);
        $res->setRegionType($array['regionType']);
        $res->setRegionTypeFull($array['regionTypeFull']);
        $res->setRegion($array['region']);
        $res->setRegionWithType($array['regionWithType']);
        $res->setRegionWithFullType($array['regionWithFullType']);
        $res->setAreaFiasId($array['areaFiasId']);
        $res->setAreaKladrId($array['areaKladrId']);
        $res->setAreaType($array['areaType']);
        $res->setAreaTypeFull($array['areaTypeFull']);
        $res->setArea($array['area']);
        $res->setAreaWithType($array['areaWithType']);
        $res->setAreaWithFullType($array['areaWithFullType']);
        $res->setCityFiasId($array['cityFiasId']);
        $res->setCityKladrId($array['cityKladrId']);
        $res->setCityType($array['cityType']);
        $res->setCityTypeFull($array['cityTypeFull']);
        $res->setCity($array['city']);
        $res->setCityWithType($array['cityWithType']);
        $res->setCityWithFullType($array['cityWithFullType']);
        $res->setSettlementFiasId($array['settlementFiasId']);
        $res->setSettlementKladrId($array['settlementKladrId']);
        $res->setSettlementType($array['settlementType']);
        $res->setSettlementTypeFull($array['settlementTypeFull']);
        $res->setSettlement($array['settlement']);
        $res->setSettlementWithType($array['settlementWithType']);
        $res->setSettlementWithFullType($array['settlementWithFullType']);
        $res->setTerritoryFiasId($array['territoryFiasId']);
        $res->setTerritoryKladrId($array['territoryKladrId']);
        $res->setTerritoryType($array['territoryType']);
        $res->setTerritoryTypeFull($array['territoryTypeFull']);
        $res->setTerritory($array['territory']);
        $res->setTerritoryWithType($array['territoryWithType']);
        $res->setTerritoryWithFullType($array['territoryWithFullType']);
        $res->setStreetFiasId($array['streetFiasId']);
        $res->setStreetKladrId($array['streetKladrId']);
        $res->setStreetType($array['streetType']);
        $res->setStreetTypeFull($array['streetTypeFull']);
        $res->setStreet($array['street']);
        $res->setStreetWithType($array['streetWithType']);
        $res->setStreetWithFullType($array['streetWithFullType']);
        $res->setHouseFiasId($array['houseFiasId']);
        $res->setHouseKladrId($array['houseKladrId']);
        $res->setHouseType($array['houseType']);
        $res->setHouseTypeFull($array['houseTypeFull']);
        $res->setHouse($array['house']);
        $res->setBlockType1($array['blockType1']);
        $res->setBlockTypeFull1($array['blockTypeFull1']);
        $res->setBlock1($array['block1']);
        $res->setBlockType2($array['blockType2']);
        $res->setBlockTypeFull2($array['blockTypeFull2']);
        $res->setBlock2($array['block2']);
        $res->setFlatFiasId($array['flatFiasId']);
        $res->setFlatType($array['flatType']);
        $res->setFlatTypeFull($array['flatTypeFull']);
        $res->setFlat($array['flat']);
        $res->setRoomFiasId($array['roomFiasId']);
        $res->setRoomType($array['roomType']);
        $res->setRoomTypeFull($array['roomTypeFull']);
        $res->setRoom($array['room']);
        $res->setSynonyms($array['synonyms']);
        $res->setRenaming($array['renaming']);
        $res->setLocationFromArray($array['location']);
        $res->setDeltaVersion($array['deltaVersion']);

        return $res;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function getFullString(bool $includeRenaming = false, int $endingAddressLevel = AddressLevel::ROOM): string
    {
        $delimiter = ', ';
        $res = $this->buildCompleteAddress($endingAddressLevel, $delimiter, 'full');

        if ($includeRenaming && !empty($this->getRenaming())) {
            // можем добавлять здесь, так как переименования хранятся только на уровне самих владельцев
            $tmp = implode($delimiter, $this->getRenaming());
            $res .= ' (бывш. ' . $tmp . ')';
        }

        return $res;
    }

    public function getShortString(bool $includeRenaming = false, int $endingAddressLevel = AddressLevel::ROOM): string
    {
        $delimiter = ', ';
        $res = $this->buildCompleteAddress($endingAddressLevel, $delimiter, 'short');

        if ($includeRenaming && !empty($this->getRenaming())) {
            // можем добавлять здесь, так как переименования хранятся только на уровне самих владельцев
            $tmp = implode($delimiter, $this->getRenaming());
            $res .= ' (бывш. ' . $tmp . ')';
        }

        return $res;
    }

    public function getStamp(string $delimiter, bool $includeRenaming = false): string
    {
        $res = $this->buildCompleteAddress(AddressLevel::ROOM, $delimiter, null);

        if ($includeRenaming && !empty($this->getRenaming())) {
            // можем добавлять здесь, так как переименования хранятся только на уровне самих владельцев
            $res .= $delimiter . implode($delimiter, $this->getRenaming());
        }

        return $res;
    }

    /**
     * Полное название дома.
     */
    public function getEntireHouse(bool $withType): ?string
    {
        $chunks = [];

        if (null !== $this->getHouse()) {
            $chunks[] = $withType
                ? implode(' ', [$this->getHouseType(), $this->getHouse()])
                : $this->getHouse();
        }

        if (null !== ($tmp = $this->getEntireHouseBlocks($withType))) {
            $chunks[] = $tmp;
        }

        $res = implode(', ', $chunks);

        return '' === $res ? null : $res;
    }

    /**
     * Полное название строений.
     * Требуется там где корпус вводится отдельно.
     */
    public function getEntireHouseBlocks(bool $withType): ?string
    {
        $chunks = [];

        if (null !== $this->getBlock1()) {
            $chunks[] = $withType
                ? implode(' ', [$this->getBlockType1(), $this->getBlock1()])
                : $this->getBlock1();
        }

        if (null !== $this->getBlock2()) {
            $chunks[] = $withType
                ? implode(' ', [$this->getBlockType2(), $this->getBlock2()])
                : $this->getBlock2();
        }

        $res = implode(', ', $chunks);

        return '' === $res ? null : $res;
    }

    public function getFiasId(): string
    {
        return $this->fiasId;
    }

    public function setFiasId(string $fiasId): void
    {
        Assert::uuid($fiasId);
        $this->fiasId = $fiasId;
    }

    public function getFiasObjectId(): int
    {
        return $this->fiasObjectId;
    }

    public function setFiasObjectId(int $fiasObjectId): void
    {
        Assert::positiveInteger($fiasObjectId);
        $this->fiasObjectId = $fiasObjectId;
    }

    public function getFiasLevel(): int
    {
        return $this->fiasLevel;
    }

    public function setFiasLevel(int $fiasLevel): void
    {
        Assert::positiveInteger($fiasLevel); // validate has
        $this->fiasLevel = $fiasLevel;
    }

    public function getAddressLevel(): int
    {
        return $this->addressLevel;
    }

    public function setAddressLevel(int $addressLevel): void
    {
        Assert::positiveInteger($addressLevel); // validate has
        $this->addressLevel = $addressLevel;
    }

    public function getRegionFiasId(): string
    {
        return $this->regionFiasId;
    }

    public function setRegionFiasId(string $regionFiasId): void
    {
        Assert::uuid($regionFiasId);
        $this->regionFiasId = $regionFiasId;
    }

    public function getRegionKladrId(): ?string
    {
        return $this->regionKladrId;
    }

    public function setRegionKladrId(?string $regionKladrId): void
    {
        Assert::nullOrStringNotEmpty($regionKladrId);
        $this->regionKladrId = $regionKladrId;
    }

    public function getRegionType(): string
    {
        return $this->regionType;
    }

    public function setRegionType(string $regionType): void
    {
        Assert::stringNotEmpty($regionType);
        $this->regionType = $regionType;
    }

    public function getRegionTypeFull(): string
    {
        return $this->regionTypeFull;
    }

    public function setRegionTypeFull(string $regionTypeFull): void
    {
        Assert::stringNotEmpty($regionTypeFull);
        $this->regionTypeFull = $regionTypeFull;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        Assert::stringNotEmpty($region);
        $this->region = $region;
    }

    public function getFlatFiasId(): ?string
    {
        return $this->flatFiasId;
    }

    public function setFlatFiasId(?string $flatFiasId): void
    {
        Assert::nullOrStringNotEmpty($flatFiasId);
        $this->flatFiasId = $flatFiasId;
    }

    public function getFlatType(): ?string
    {
        return $this->flatType;
    }

    public function setFlatType(?string $flatType): void
    {
        Assert::nullOrStringNotEmpty($flatType);
        $this->flatType = $flatType;
    }

    public function getFlatTypeFull(): ?string
    {
        return $this->flatTypeFull;
    }

    public function setFlatTypeFull(?string $flatTypeFull): void
    {
        Assert::nullOrStringNotEmpty($flatTypeFull);
        $this->flatTypeFull = $flatTypeFull;
    }

    public function getFlat(): ?string
    {
        return $this->flat;
    }

    public function setFlat(?string $flat): void
    {
        Assert::nullOrStringNotEmpty($flat);
        $this->flat = $flat;
    }

    public function getCityFiasId(): ?string
    {
        return $this->cityFiasId;
    }

    public function setCityFiasId(?string $cityFiasId): void
    {
        Assert::nullOrStringNotEmpty($cityFiasId);
        $this->cityFiasId = $cityFiasId;
    }

    public function getCityKladrId(): ?string
    {
        return $this->cityKladrId;
    }

    public function setCityKladrId(?string $cityKladrId): void
    {
        Assert::nullOrStringNotEmpty($cityKladrId);
        $this->cityKladrId = $cityKladrId;
    }

    public function getCityType(): ?string
    {
        return $this->cityType;
    }

    public function setCityType(?string $cityType): void
    {
        Assert::nullOrStringNotEmpty($cityType);
        $this->cityType = $cityType;
    }

    public function getCityTypeFull(): ?string
    {
        return $this->cityTypeFull;
    }

    public function setCityTypeFull(?string $cityTypeFull): void
    {
        Assert::nullOrStringNotEmpty($cityTypeFull);
        $this->cityTypeFull = $cityTypeFull;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        Assert::nullOrStringNotEmpty($city);
        $this->city = $city;
    }

    public function getSettlementFiasId(): ?string
    {
        return $this->settlementFiasId;
    }

    public function setSettlementFiasId(?string $settlementFiasId): void
    {
        Assert::nullOrStringNotEmpty($settlementFiasId);
        $this->settlementFiasId = $settlementFiasId;
    }

    public function getSettlementKladrId(): ?string
    {
        return $this->settlementKladrId;
    }

    public function setSettlementKladrId(?string $settlementKladrId): void
    {
        Assert::nullOrStringNotEmpty($settlementKladrId);
        $this->settlementKladrId = $settlementKladrId;
    }

    public function getSettlementType(): ?string
    {
        return $this->settlementType;
    }

    public function setSettlementType(?string $settlementType): void
    {
        Assert::nullOrStringNotEmpty($settlementType);
        $this->settlementType = $settlementType;
    }

    public function getSettlementTypeFull(): ?string
    {
        return $this->settlementTypeFull;
    }

    public function setSettlementTypeFull(?string $settlementTypeFull): void
    {
        Assert::nullOrStringNotEmpty($settlementTypeFull);
        $this->settlementTypeFull = $settlementTypeFull;
    }

    public function getSettlement(): ?string
    {
        return $this->settlement;
    }

    public function setSettlement(?string $settlement): void
    {
        Assert::nullOrStringNotEmpty($settlement);
        $this->settlement = $settlement;
    }

    public function getStreetFiasId(): ?string
    {
        return $this->streetFiasId;
    }

    public function setStreetFiasId(?string $streetFiasId): void
    {
        Assert::nullOrUuid($streetFiasId);
        $this->streetFiasId = $streetFiasId;
    }

    public function getStreetKladrId(): ?string
    {
        return $this->streetKladrId;
    }

    public function setStreetKladrId(?string $streetKladrId): void
    {
        Assert::nullOrStringNotEmpty($streetKladrId);
        $this->streetKladrId = $streetKladrId;
    }

    public function getStreetType(): ?string
    {
        return $this->streetType;
    }

    public function setStreetType(?string $streetType): void
    {
        Assert::nullOrStringNotEmpty($streetType);
        $this->streetType = $streetType;
    }

    public function getStreetTypeFull(): ?string
    {
        return $this->streetTypeFull;
    }

    public function setStreetTypeFull(?string $streetTypeFull): void
    {
        Assert::nullOrStringNotEmpty($streetTypeFull);
        $this->streetTypeFull = $streetTypeFull;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        Assert::nullOrStringNotEmpty($street);
        $this->street = $street;
    }

    public function getHouseFiasId(): ?string
    {
        return $this->houseFiasId;
    }

    public function setHouseFiasId(?string $houseFiasId): void
    {
        Assert::nullOrUuid($houseFiasId);
        $this->houseFiasId = $houseFiasId;
    }

    public function getHouseKladrId(): ?string
    {
        return $this->houseKladrId;
    }

    public function setHouseKladrId(?string $houseKladrId): void
    {
        Assert::nullOrStringNotEmpty($houseKladrId);
        $this->houseKladrId = $houseKladrId;
    }

    public function getHouseType(): ?string
    {
        return $this->houseType;
    }

    public function setHouseType(?string $houseType): void
    {
        Assert::nullOrStringNotEmpty($houseType);
        $this->houseType = $houseType;
    }

    public function getHouseTypeFull(): ?string
    {
        return $this->houseTypeFull;
    }

    public function setHouseTypeFull(?string $houseTypeFull): void
    {
        Assert::nullOrStringNotEmpty($houseTypeFull);
        $this->houseTypeFull = $houseTypeFull;
    }

    public function getHouse(): ?string
    {
        return $this->house;
    }

    public function setHouse(?string $house): void
    {
        Assert::nullOrStringNotEmpty($house);
        $this->house = $house;
    }

    public function getBlockType1(): ?string
    {
        return $this->blockType1;
    }

    public function setBlockType1(?string $blockType1): void
    {
        Assert::nullOrStringNotEmpty($blockType1);
        $this->blockType1 = $blockType1;
    }

    public function getBlockTypeFull1(): ?string
    {
        return $this->blockTypeFull1;
    }

    public function setBlockTypeFull1(?string $blockTypeFull1): void
    {
        Assert::nullOrStringNotEmpty($blockTypeFull1);
        $this->blockTypeFull1 = $blockTypeFull1;
    }

    public function getBlockType2(): ?string
    {
        return $this->blockType2;
    }

    public function setBlockType2(?string $blockType2): void
    {
        Assert::nullOrStringNotEmpty($blockType2);
        $this->blockType2 = $blockType2;
    }

    public function getBlockTypeFull2(): ?string
    {
        return $this->blockTypeFull2;
    }

    public function setBlockTypeFull2(?string $blockTypeFull2): void
    {
        Assert::nullOrStringNotEmpty($blockTypeFull2);
        $this->blockTypeFull2 = $blockTypeFull2;
    }

    public function getBlock2(): ?string
    {
        return $this->block2;
    }

    public function setBlock2(?string $block2): void
    {
        Assert::nullOrStringNotEmpty($block2);
        $this->block2 = $block2;
    }

    public function getBlock1(): ?string
    {
        return $this->block1;
    }

    public function setBlock1(?string $block1): void
    {
        Assert::nullOrStringNotEmpty($block1);
        $this->block1 = $block1;
    }

    public function getAreaFiasId(): ?string
    {
        return $this->areaFiasId;
    }

    public function setAreaFiasId(?string $areaFiasId): void
    {
        Assert::nullOrUuid($areaFiasId);
        $this->areaFiasId = $areaFiasId;
    }

    public function getAreaKladrId(): ?string
    {
        return $this->areaKladrId;
    }

    public function setAreaKladrId(?string $areaKladrId): void
    {
        Assert::nullOrStringNotEmpty($areaKladrId);
        $this->areaKladrId = $areaKladrId;
    }

    public function getAreaType(): ?string
    {
        return $this->areaType;
    }

    public function setAreaType(?string $areaType): void
    {
        Assert::nullOrStringNotEmpty($areaType);
        $this->areaType = $areaType;
    }

    public function getAreaTypeFull(): ?string
    {
        return $this->areaTypeFull;
    }

    public function setAreaTypeFull(?string $areaTypeFull): void
    {
        Assert::nullOrStringNotEmpty($areaTypeFull);
        $this->areaTypeFull = $areaTypeFull;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): void
    {
        Assert::nullOrStringNotEmpty($area);
        $this->area = $area;
    }

    public function getKladrId(): ?string
    {
        return $this->kladrId;
    }

    public function setKladrId(?string $kladrId): void
    {
        Assert::nullOrStringNotEmpty($kladrId);
        $this->kladrId = $kladrId;
    }

    public function getOkato(): ?string
    {
        return $this->okato;
    }

    public function setOkato(?string $okato): void
    {
        Assert::nullOrStringNotEmpty($okato);
        $this->okato = $okato;
    }

    public function getOktmo(): ?string
    {
        return $this->oktmo;
    }

    public function setOktmo(?string $oktmo): void
    {
        Assert::nullOrStringNotEmpty($oktmo);
        $this->oktmo = $oktmo;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        Assert::nullOrStringNotEmpty($postalCode);
        $this->postalCode = $postalCode;
    }

    public function getRoomFiasId(): ?string
    {
        return $this->roomFiasId;
    }

    public function setRoomFiasId(?string $roomFiasId): void
    {
        Assert::nullOrStringNotEmpty($roomFiasId);
        $this->roomFiasId = $roomFiasId;
    }

    public function getRoomType(): ?string
    {
        return $this->roomType;
    }

    public function setRoomType(?string $roomType): void
    {
        Assert::nullOrStringNotEmpty($roomType);
        $this->roomType = $roomType;
    }

    public function getRoomTypeFull(): ?string
    {
        return $this->roomTypeFull;
    }

    public function setRoomTypeFull(?string $roomTypeFull): void
    {
        Assert::nullOrStringNotEmpty($roomTypeFull);
        $this->roomTypeFull = $roomTypeFull;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): void
    {
        Assert::nullOrStringNotEmpty($room);
        $this->room = $room;
    }

    public function getSynonyms(): ?array
    {
        return $this->synonyms;
    }

    public function setSynonyms(?array $synonyms): void
    {
        Assert::nullOrNotEmpty($synonyms);
        $this->synonyms = $synonyms;
    }

    public function getRenaming(): ?array
    {
        return $this->renaming;
    }

    public function setRenaming(?array $renaming): void
    {
        Assert::nullOrNotEmpty($renaming);
        $this->renaming = $renaming;
    }

    public function getTerritoryFiasId(): ?string
    {
        return $this->territoryFiasId;
    }

    public function setTerritoryFiasId(?string $territoryFiasId): void
    {
        Assert::nullOrStringNotEmpty($territoryFiasId);
        $this->territoryFiasId = $territoryFiasId;
    }

    public function getTerritoryKladrId(): ?string
    {
        return $this->territoryKladrId;
    }

    public function setTerritoryKladrId(?string $territoryKladrId): void
    {
        Assert::nullOrStringNotEmpty($territoryKladrId);
        $this->territoryKladrId = $territoryKladrId;
    }

    public function getTerritoryType(): ?string
    {
        return $this->territoryType;
    }

    public function setTerritoryType(?string $territoryType): void
    {
        Assert::nullOrStringNotEmpty($territoryType);
        $this->territoryType = $territoryType;
    }

    public function getTerritoryTypeFull(): ?string
    {
        return $this->territoryTypeFull;
    }

    public function setTerritoryTypeFull(?string $territoryTypeFull): void
    {
        Assert::nullOrStringNotEmpty($territoryTypeFull);
        $this->territoryTypeFull = $territoryTypeFull;
    }

    public function getTerritory(): ?string
    {
        return $this->territory;
    }

    public function setTerritory(?string $territory): void
    {
        Assert::nullOrStringNotEmpty($territory);
        $this->territory = $territory;
    }

    public function getRegionWithType(): string
    {
        return $this->regionWithType;
    }

    public function setRegionWithType(string $regionWithType): void
    {
        Assert::stringNotEmpty($regionWithType);
        $this->regionWithType = $regionWithType;
    }

    public function getAreaWithType(): ?string
    {
        return $this->areaWithType;
    }

    public function setAreaWithType(?string $areaWithType): void
    {
        Assert::nullOrStringNotEmpty($areaWithType);
        $this->areaWithType = $areaWithType;
    }

    public function getCityWithType(): ?string
    {
        return $this->cityWithType;
    }

    public function setCityWithType(?string $cityWithType): void
    {
        Assert::nullOrStringNotEmpty($cityWithType);
        $this->cityWithType = $cityWithType;
    }

    public function getSettlementWithType(): ?string
    {
        return $this->settlementWithType;
    }

    public function setSettlementWithType(?string $settlementWithType): void
    {
        Assert::nullOrStringNotEmpty($settlementWithType);
        $this->settlementWithType = $settlementWithType;
    }

    public function getTerritoryWithType(): ?string
    {
        return $this->territoryWithType;
    }

    public function setTerritoryWithType(?string $territoryWithType): void
    {
        Assert::nullOrStringNotEmpty($territoryWithType);
        $this->territoryWithType = $territoryWithType;
    }

    public function getStreetWithType(): ?string
    {
        return $this->streetWithType;
    }

    public function setStreetWithType(?string $streetWithType): void
    {
        Assert::nullOrStringNotEmpty($streetWithType);
        $this->streetWithType = $streetWithType;
    }

    public function getLocation(): ?array
    {
        return $this->location;
    }

    public function setLocationFromLonLat(float $lon, float $lat): void
    {
        $this->setLocationFromArray([$lon, $lat]);
    }

    public function setLocationFromArray(?array $location): void
    {
        Assert::nullOrNotEmpty($location);

        if (null !== $location) {
            Assert::isList($location);
            Assert::count($location, 2);
        }

        $this->location = $location;
    }

    public function getRegionWithFullType(): string
    {
        return $this->regionWithFullType;
    }

    public function setRegionWithFullType(string $regionWithFullType): void
    {
        Assert::stringNotEmpty($regionWithFullType);
        $this->regionWithFullType = $regionWithFullType;
    }

    public function getAreaWithFullType(): ?string
    {
        return $this->areaWithFullType;
    }

    public function setAreaWithFullType(?string $areaWithFullType): void
    {
        Assert::nullOrStringNotEmpty($areaWithFullType);
        $this->areaWithFullType = $areaWithFullType;
    }

    public function getCityWithFullType(): ?string
    {
        return $this->cityWithFullType;
    }

    public function setCityWithFullType(?string $cityWithFullType): void
    {
        Assert::nullOrStringNotEmpty($cityWithFullType);
        $this->cityWithFullType = $cityWithFullType;
    }

    public function getSettlementWithFullType(): ?string
    {
        return $this->settlementWithFullType;
    }

    public function setSettlementWithFullType(?string $settlementWithFullType): void
    {
        Assert::nullOrStringNotEmpty($settlementWithFullType);
        $this->settlementWithFullType = $settlementWithFullType;
    }

    public function getTerritoryWithFullType(): ?string
    {
        return $this->territoryWithFullType;
    }

    public function setTerritoryWithFullType(?string $territoryWithFullType): void
    {
        Assert::nullOrStringNotEmpty($territoryWithFullType);
        $this->territoryWithFullType = $territoryWithFullType;
    }

    public function getStreetWithFullType(): ?string
    {
        return $this->streetWithFullType;
    }

    public function setStreetWithFullType(?string $streetWithFullType): void
    {
        Assert::nullOrStringNotEmpty($streetWithFullType);
        $this->streetWithFullType = $streetWithFullType;
    }

    public function getDeltaVersion(): int
    {
        return $this->deltaVersion;
    }

    public function setDeltaVersion(int $deltaVersion): void
    {
        // Assert::greaterThan($deltaVersion, 0);
        $this->deltaVersion = $deltaVersion;
    }

    private function buildCompleteAddress(int $endingAddressLevel, string $delimiter, ?string $includeType): string
    {
        if (!in_array($includeType, [null, 'short', 'full'], true)) {
            throw new \RuntimeException(sprintf('Illegal includeType "%s".', $includeType));
        }

        $chunks = [];

        $parentLevels = AddressLevel::getTree($endingAddressLevel);
        foreach ($parentLevels as $level) {
            switch ($level) {
                case AddressLevel::REGION:
                    if ('' !== $this->getRegion()) {
                        switch ($includeType) {
                            case 'short':
                                $chunks[] = $this->getRegionWithType();
                                break;
                            case 'full':
                                $chunks[] = $this->getRegionWithFullType();
                                break;
                            case null:
                                $chunks[] = $this->getRegion();
                        }
                    }
                    break;
                case AddressLevel::AREA:
                    if (null !== $this->getArea()) {
                        switch ($includeType) {
                            case 'short':
                                $chunks[] = $this->getAreaWithType();
                                break;
                            case 'full':
                                $chunks[] = $this->getAreaWithFullType();
                                break;
                            case null:
                                $chunks[] = $this->getArea();
                        }
                    }
                    break;
                case AddressLevel::CITY:
                    if (null !== $this->getCity()) {
                        switch ($includeType) {
                            case 'short':
                                $chunks[] = $this->getCityWithType();
                                break;
                            case 'full':
                                $chunks[] = $this->getCityWithFullType();
                                break;
                            case null:
                                $chunks[] = $this->getCity();
                        }
                    }
                    break;
                case AddressLevel::SETTLEMENT:
                    if (null !== $this->getSettlement()) {
                        switch ($includeType) {
                            case 'short':
                                $chunks[] = $this->getSettlementWithType();
                                break;
                            case 'full':
                                $chunks[] = $this->getSettlementWithFullType();
                                break;
                            case null:
                                $chunks[] = $this->getSettlement();
                        }
                    }
                    break;
                case AddressLevel::TERRITORY:
                    if (null !== $this->getTerritory()) {
                        switch ($includeType) {
                            case 'short':
                                $chunks[] = $this->getTerritoryWithType();
                                break;
                            case 'full':
                                $chunks[] = $this->getTerritoryWithFullType();
                                break;
                            case null:
                                $chunks[] = $this->getTerritory();
                        }
                    }
                    break;
                case AddressLevel::STREET:
                    if (null !== $this->getStreet()) {
                        switch ($includeType) {
                            case 'short':
                                $chunks[] = $this->getStreetWithType();
                                break;
                            case 'full':
                                $chunks[] = $this->getStreetWithFullType();
                                break;
                            case null:
                                $chunks[] = $this->getStreet();
                        }
                    }
                    break;
                case AddressLevel::HOUSE:
                    if (null !== ($entireHouse = $this->getEntireHouse(null !== $includeType))) {
                        $chunks[] = $entireHouse;
                    }
                    break;
                case AddressLevel::FLAT:
                    if (null !== $this->getFlat()) {
                        $chunks[] = $includeType
                            ? implode(' ', [$this->getFlatType(), $this->getFlat()])
                            : $this->getFlat();
                    }
                    break;
                case AddressLevel::ROOM:
                    if (null !== $this->getRoom()) {
                        $chunks[] = $includeType
                            ? implode(' ', [$this->getRoomType(), $this->getRoom()])
                            : $this->getRoom();
                    }
                    break;
                case AddressLevel::STEAD:
                case AddressLevel::CAR_PLACE:
                    // эти уровни не индексируем, таким образом сюда они попадать не должны
                    throw new InvalidAddressLevelException(sprintf('Unsupported address level "%d".', $level));
            }
        }

        return implode($delimiter, $chunks);
    }
}
