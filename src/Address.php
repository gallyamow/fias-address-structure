<?php

declare(strict_types=1);

namespace Addresser\AddressRepository;

use Webmozart\Assert\Assert;

/**
 * Плоская структура для хранения адреса.
 *
 * Одна и та же структура будет использована как для хранения информации до районов, так и для хранения
 * информации до квартир.
 *
 * Здесь присутствует некоторая денормализация: поля предыдущих уровней и *withType - для упрощения работы клиентов
 * (нет необходимости знать с какой стороны прибавлять type), поля *type, *typeFull - как справочная информация.
 *
 * Для уровней ниже street полей withType - нет, потому что у них всегда тип стоит в NAME_POSITION_BEFORE.
 */
class Address implements \JsonSerializable
{
    /**
     * ФИАС-код адреса (уровня)
     * @var string
     */
    private string $fiasId;

    /**
     * Это поле не относится к адресу и нужно для возобновления индексации.
     * @var int
     */
    private int $fiasHierarchyId;

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
     * Поля региона
     */
    private string $regionFiasId;
    private ?string $regionKladrId;
    private string $regionType;
    private string $regionTypeFull;
    private string $region;
    private string $regionWithType;

    /**
     * Поля района внутри региона.
     */
    private ?string $areaFiasId = null;
    private ?string $areaKladrId = null;
    private ?string $areaType = null;
    private ?string $areaTypeFull = null;
    private ?string $area = null;
    private ?string $areaWithType = null;

    /**
     * Поля города
     */
    private ?string $cityFiasId = null;
    private ?string $cityKladrId = null;
    private ?string $cityType = null;
    private ?string $cityTypeFull = null;
    private ?string $city = null;
    private ?string $cityWithType = null;

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

    /**
     * Поля территории (внутри города или района).
     * Сюда попадают СНТ, тер гк, зоны, гск
     */
    private ?string $territoryFiasId = null;
    private ?string $territoryKladrId = null;
    private ?string $territoryType = null;
    private ?string $territoryTypeFull = null;
    private ?string $territory = null;
    private ?string $territoryWithType = null;

    /**
     * Поля улицы
     */
    private ?string $streetFiasId = null;
    private ?string $streetKladrId = null;
    private ?string $streetType = null;
    private ?string $streetTypeFull = null;
    private ?string $street = null;
    private ?string $streetWithType = null;

    /**
     * Поля дома
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
     * Поля квартиры
     */
    private ?string $flatFiasId = null;
    private ?string $flatType = null;
    private ?string $flatTypeFull = null;
    private ?string $flat = null;

    /**
     * Поля помещения
     */
    private ?string $roomFiasId = null;
    private ?string $roomType = null;
    private ?string $roomTypeFull = null;
    private ?string $room = null;

    /**
     * Синонимы - различные сокращенные названия вида Башкирия, Татария, ХМАО.
     * @var array
     */
    private ?array $synonyms = null;

    /**
     * Старые названия - переименования регионов, городов, улиц.
     * Заполняться будет только на необходимом уровне. Для дочерних - не будет.
     * @var array
     */
    private ?array $renaming = null;

    /**
     * Геопозиция.
     * @var array [lon, lat]
     */
    private ?array $location = null;

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function getCompleteShortAddress(): string
    {
        $chunks = [
            $this->getRegionWithType(),
        ];

        if (null !== $this->getArea()) {
            $chunks[] = $this->getAreaWithType();
        }

        if (null !== $this->getCity()) {
            $chunks[] = $this->getCityWithType();
        }

        if (null !== $this->getSettlement()) {
            $chunks[] = $this->getSettlementWithType();
        }

        if (null !== $this->getTerritory()) {
            $chunks[] = $this->getTerritoryWithType();
        }

        if (null !== $this->getStreet()) {
            $chunks[] = $this->getStreetWithType();
        }

        if (null !== $this->getHouse()) {
            $chunks[] = implode(' ', [$this->getHouseType(), $this->getHouse()]);
        }

        if (null !== $this->getBlock1()) {
            $chunks[] = implode(' ', [$this->getBlockType1(), $this->getBlock1()]);
        }

        if (null !== $this->getBlock2()) {
            $chunks[] = implode(' ', [$this->getBlockType2(), $this->getBlock2()]);
        }

        if (null !== $this->getFlat()) {
            $chunks[] = implode(' ', [$this->getFlatType(), $this->getFlat()]);
        }

        if (null !== $this->getRoom()) {
            $chunks[] = implode(' ', [$this->getRoomType(), $this->getRoom()]);
        }

        // показываем все переименования
        if (!empty($this->getRenaming())) {
            $chunks[] = '(бывш. '.implode(', ', $this->getRenaming()).')';
        }

        return implode(', ', $chunks);
    }

    /**
     * @return string
     */
    public function getFiasId(): string
    {
        return $this->fiasId;
    }

    /**
     * @param string $fiasId
     */
    public function setFiasId(string $fiasId): void
    {
        Assert::uuid($fiasId);
        $this->fiasId = $fiasId;
    }

    /**
     * @return int
     */
    public function getFiasHierarchyId(): int
    {
        return $this->fiasHierarchyId;
    }

    /**
     * @param int $fiasHierarchyId
     */
    public function setFiasHierarchyId(int $fiasHierarchyId): void
    {
        Assert::positiveInteger($fiasHierarchyId);
        $this->fiasHierarchyId = $fiasHierarchyId;
    }

    /**
     * @return int
     */
    public function getFiasLevel(): int
    {
        return $this->fiasLevel;
    }

    /**
     * @param int $fiasLevel
     */
    public function setFiasLevel(int $fiasLevel): void
    {
        Assert::positiveInteger($fiasLevel);
        $this->fiasLevel = $fiasLevel;
    }

    /**
     * @return int
     */
    public function getAddressLevel(): int
    {
        return $this->addressLevel;
    }

    /**
     * @param int $addressLevel
     */
    public function setAddressLevel(int $addressLevel): void
    {
        Assert::positiveInteger($addressLevel);
        $this->addressLevel = $addressLevel;
    }

    /**
     * @return string
     */
    public function getRegionFiasId(): string
    {
        return $this->regionFiasId;
    }

    /**
     * @param string $regionFiasId
     */
    public function setRegionFiasId(string $regionFiasId): void
    {
        Assert::uuid($regionFiasId);
        $this->regionFiasId = $regionFiasId;
    }

    /**
     * @return string|null
     */
    public function getRegionKladrId(): ?string
    {
        return $this->regionKladrId;
    }

    /**
     * @param string|null $regionKladrId
     */
    public function setRegionKladrId(?string $regionKladrId): void
    {
        Assert::nullOrStringNotEmpty($regionKladrId);
        $this->regionKladrId = $regionKladrId;
    }

    /**
     * @return string
     */
    public function getRegionType(): string
    {
        return $this->regionType;
    }

    /**
     * @param string $regionType
     */
    public function setRegionType(string $regionType): void
    {
        Assert::stringNotEmpty($regionType);
        $this->regionType = $regionType;
    }

    /**
     * @return string
     */
    public function getRegionTypeFull(): string
    {
        return $this->regionTypeFull;
    }

    /**
     * @param string $regionTypeFull
     */
    public function setRegionTypeFull(string $regionTypeFull): void
    {
        Assert::stringNotEmpty($regionTypeFull);
        $this->regionTypeFull = $regionTypeFull;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        Assert::stringNotEmpty($region);
        $this->region = $region;
    }

    /**
     * @return string|null
     */
    public function getFlatFiasId(): ?string
    {
        return $this->flatFiasId;
    }

    /**
     * @param string|null $flatFiasId
     */
    public function setFlatFiasId(?string $flatFiasId): void
    {
        Assert::nullOrStringNotEmpty($flatFiasId);
        $this->flatFiasId = $flatFiasId;
    }

    /**
     * @return string|null
     */
    public function getFlatType(): ?string
    {
        return $this->flatType;
    }

    /**
     * @param string|null $flatType
     */
    public function setFlatType(?string $flatType): void
    {
        Assert::nullOrStringNotEmpty($flatType);
        $this->flatType = $flatType;
    }

    /**
     * @return string|null
     */
    public function getFlatTypeFull(): ?string
    {
        return $this->flatTypeFull;
    }

    /**
     * @param string|null $flatTypeFull
     */
    public function setFlatTypeFull(?string $flatTypeFull): void
    {
        Assert::nullOrStringNotEmpty($flatTypeFull);
        $this->flatTypeFull = $flatTypeFull;
    }

    /**
     * @return string|null
     */
    public function getFlat(): ?string
    {
        return $this->flat;
    }

    /**
     * @param string|null $flat
     */
    public function setFlat(?string $flat): void
    {
        Assert::nullOrStringNotEmpty($flat);
        $this->flat = $flat;
    }

    /**
     * @return string|null
     */
    public function getCityFiasId(): ?string
    {
        return $this->cityFiasId;
    }

    /**
     * @param string|null $cityFiasId
     */
    public function setCityFiasId(?string $cityFiasId): void
    {
        Assert::nullOrStringNotEmpty($cityFiasId);
        $this->cityFiasId = $cityFiasId;
    }

    /**
     * @return string|null
     */
    public function getCityKladrId(): ?string
    {
        return $this->cityKladrId;
    }

    /**
     * @param string|null $cityKladrId
     */
    public function setCityKladrId(?string $cityKladrId): void
    {
        Assert::nullOrStringNotEmpty($cityKladrId);
        $this->cityKladrId = $cityKladrId;
    }

    /**
     * @return string|null
     */
    public function getCityType(): ?string
    {
        return $this->cityType;
    }

    /**
     * @param string|null $cityType
     */
    public function setCityType(?string $cityType): void
    {
        Assert::nullOrStringNotEmpty($cityType);
        $this->cityType = $cityType;
    }

    /**
     * @return string|null
     */
    public function getCityTypeFull(): ?string
    {
        return $this->cityTypeFull;
    }

    /**
     * @param string|null $cityTypeFull
     */
    public function setCityTypeFull(?string $cityTypeFull): void
    {
        Assert::nullOrStringNotEmpty($cityTypeFull);
        $this->cityTypeFull = $cityTypeFull;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        Assert::nullOrStringNotEmpty($city);
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getSettlementFiasId(): ?string
    {
        return $this->settlementFiasId;
    }

    /**
     * @param string|null $settlementFiasId
     */
    public function setSettlementFiasId(?string $settlementFiasId): void
    {
        Assert::nullOrStringNotEmpty($settlementFiasId);
        $this->settlementFiasId = $settlementFiasId;
    }

    /**
     * @return string|null
     */
    public function getSettlementKladrId(): ?string
    {
        return $this->settlementKladrId;
    }

    /**
     * @param string|null $settlementKladrId
     */
    public function setSettlementKladrId(?string $settlementKladrId): void
    {
        Assert::nullOrStringNotEmpty($settlementKladrId);
        $this->settlementKladrId = $settlementKladrId;
    }

    /**
     * @return string|null
     */
    public function getSettlementType(): ?string
    {
        return $this->settlementType;
    }

    /**
     * @param string|null $settlementType
     */
    public function setSettlementType(?string $settlementType): void
    {
        Assert::nullOrStringNotEmpty($settlementType);
        $this->settlementType = $settlementType;
    }

    /**
     * @return string|null
     */
    public function getSettlementTypeFull(): ?string
    {
        return $this->settlementTypeFull;
    }

    /**
     * @param string|null $settlementTypeFull
     */
    public function setSettlementTypeFull(?string $settlementTypeFull): void
    {
        Assert::nullOrStringNotEmpty($settlementTypeFull);
        $this->settlementTypeFull = $settlementTypeFull;
    }

    /**
     * @return string|null
     */
    public function getSettlement(): ?string
    {
        return $this->settlement;
    }

    /**
     * @param string|null $settlement
     */
    public function setSettlement(?string $settlement): void
    {
        Assert::nullOrStringNotEmpty($settlement);
        $this->settlement = $settlement;
    }

    /**
     * @return string|null
     */
    public function getStreetFiasId(): ?string
    {
        return $this->streetFiasId;
    }

    /**
     * @param string|null $streetFiasId
     */
    public function setStreetFiasId(?string $streetFiasId): void
    {
        Assert::nullOrUuid($streetFiasId);
        $this->streetFiasId = $streetFiasId;
    }

    /**
     * @return string|null
     */
    public function getStreetKladrId(): ?string
    {
        return $this->streetKladrId;
    }

    /**
     * @param string|null $streetKladrId
     */
    public function setStreetKladrId(?string $streetKladrId): void
    {
        Assert::nullOrStringNotEmpty($streetKladrId);
        $this->streetKladrId = $streetKladrId;
    }

    /**
     * @return string|null
     */
    public function getStreetType(): ?string
    {
        return $this->streetType;
    }

    /**
     * @param string|null $streetType
     */
    public function setStreetType(?string $streetType): void
    {
        Assert::nullOrStringNotEmpty($streetType);
        $this->streetType = $streetType;
    }

    /**
     * @return string|null
     */
    public function getStreetTypeFull(): ?string
    {
        return $this->streetTypeFull;
    }

    /**
     * @param string|null $streetTypeFull
     */
    public function setStreetTypeFull(?string $streetTypeFull): void
    {
        Assert::nullOrStringNotEmpty($streetTypeFull);
        $this->streetTypeFull = $streetTypeFull;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street): void
    {
        Assert::nullOrStringNotEmpty($street);
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getHouseFiasId(): ?string
    {
        return $this->houseFiasId;
    }

    /**
     * @param string|null $houseFiasId
     */
    public function setHouseFiasId(?string $houseFiasId): void
    {
        Assert::nullOrUuid($houseFiasId);
        $this->houseFiasId = $houseFiasId;
    }

    /**
     * @return string|null
     */
    public function getHouseKladrId(): ?string
    {
        return $this->houseKladrId;
    }

    /**
     * @param string|null $houseKladrId
     */
    public function setHouseKladrId(?string $houseKladrId): void
    {
        Assert::nullOrStringNotEmpty($houseKladrId);
        $this->houseKladrId = $houseKladrId;
    }

    /**
     * @return string|null
     */
    public function getHouseType(): ?string
    {
        return $this->houseType;
    }

    /**
     * @param string|null $houseType
     */
    public function setHouseType(?string $houseType): void
    {
        Assert::nullOrStringNotEmpty($houseType);
        $this->houseType = $houseType;
    }

    /**
     * @return string|null
     */
    public function getHouseTypeFull(): ?string
    {
        return $this->houseTypeFull;
    }

    /**
     * @param string|null $houseTypeFull
     */
    public function setHouseTypeFull(?string $houseTypeFull): void
    {
        Assert::nullOrStringNotEmpty($houseTypeFull);
        $this->houseTypeFull = $houseTypeFull;
    }

    /**
     * @return string|null
     */
    public function getHouse(): ?string
    {
        return $this->house;
    }

    /**
     * @param string|null $house
     */
    public function setHouse(?string $house): void
    {
        Assert::nullOrStringNotEmpty($house);
        $this->house = $house;
    }

    /**
     * @return string|null
     */
    public function getBlockType1(): ?string
    {
        return $this->blockType1;
    }

    /**
     * @param string|null $blockType1
     */
    public function setBlockType1(?string $blockType1): void
    {
        Assert::nullOrStringNotEmpty($blockType1);
        $this->blockType1 = $blockType1;
    }

    /**
     * @return string|null
     */
    public function getBlockTypeFull1(): ?string
    {
        return $this->blockTypeFull1;
    }

    /**
     * @param string|null $blockTypeFull1
     */
    public function setBlockTypeFull1(?string $blockTypeFull1): void
    {
        Assert::nullOrStringNotEmpty($blockTypeFull1);
        $this->blockTypeFull1 = $blockTypeFull1;
    }

    /**
     * @return string|null
     */
    public function getBlockType2(): ?string
    {
        return $this->blockType2;
    }

    /**
     * @param string|null $blockType2
     */
    public function setBlockType2(?string $blockType2): void
    {
        Assert::nullOrStringNotEmpty($blockType2);
        $this->blockType2 = $blockType2;
    }

    /**
     * @return string|null
     */
    public function getBlockTypeFull2(): ?string
    {
        return $this->blockTypeFull2;
    }

    /**
     * @param string|null $blockTypeFull2
     */
    public function setBlockTypeFull2(?string $blockTypeFull2): void
    {
        Assert::nullOrStringNotEmpty($blockTypeFull2);
        $this->blockTypeFull2 = $blockTypeFull2;
    }

    /**
     * @return string|null
     */
    public function getBlock2(): ?string
    {
        return $this->block2;
    }

    /**
     * @param string|null $block2
     */
    public function setBlock2(?string $block2): void
    {
        Assert::nullOrStringNotEmpty($block2);
        $this->block2 = $block2;
    }

    /**
     * @return string|null
     */
    public function getBlock1(): ?string
    {
        return $this->block1;
    }

    /**
     * @param string|null $block1
     */
    public function setBlock1(?string $block1): void
    {
        Assert::nullOrStringNotEmpty($block1);
        $this->block1 = $block1;
    }

    /**
     * @return string|null
     */
    public function getAreaFiasId(): ?string
    {
        return $this->areaFiasId;
    }

    /**
     * @param string|null $areaFiasId
     */
    public function setAreaFiasId(?string $areaFiasId): void
    {
        Assert::nullOrUuid($areaFiasId);
        $this->areaFiasId = $areaFiasId;
    }

    /**
     * @return string|null
     */
    public function getAreaKladrId(): ?string
    {
        return $this->areaKladrId;
    }

    /**
     * @param string|null $areaKladrId
     */
    public function setAreaKladrId(?string $areaKladrId): void
    {
        Assert::nullOrStringNotEmpty($areaKladrId);
        $this->areaKladrId = $areaKladrId;
    }

    /**
     * @return string|null
     */
    public function getAreaType(): ?string
    {
        return $this->areaType;
    }

    /**
     * @param string|null $areaType
     */
    public function setAreaType(?string $areaType): void
    {
        Assert::nullOrStringNotEmpty($areaType);
        $this->areaType = $areaType;
    }

    /**
     * @return string|null
     */
    public function getAreaTypeFull(): ?string
    {
        return $this->areaTypeFull;
    }

    /**
     * @param string|null $areaTypeFull
     */
    public function setAreaTypeFull(?string $areaTypeFull): void
    {
        Assert::nullOrStringNotEmpty($areaTypeFull);
        $this->areaTypeFull = $areaTypeFull;
    }

    /**
     * @return string|null
     */
    public function getArea(): ?string
    {
        return $this->area;
    }

    /**
     * @param string|null $area
     */
    public function setArea(?string $area): void
    {
        Assert::nullOrStringNotEmpty($area);
        $this->area = $area;
    }

    /**
     * @return string|null
     */
    public function getKladrId(): ?string
    {
        return $this->kladrId;
    }

    /**
     * @param string|null $kladrId
     */
    public function setKladrId(?string $kladrId): void
    {
        Assert::nullOrStringNotEmpty($kladrId);
        $this->kladrId = $kladrId;
    }

    /**
     * @return string|null
     */
    public function getOkato(): ?string
    {
        return $this->okato;
    }

    /**
     * @param string|null $okato
     */
    public function setOkato(?string $okato): void
    {
        Assert::nullOrStringNotEmpty($okato);
        $this->okato = $okato;
    }

    /**
     * @return string|null
     */
    public function getOktmo(): ?string
    {
        return $this->oktmo;
    }

    /**
     * @param string|null $oktmo
     */
    public function setOktmo(?string $oktmo): void
    {
        Assert::nullOrStringNotEmpty($oktmo);
        $this->oktmo = $oktmo;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string|null $postalCode
     */
    public function setPostalCode(?string $postalCode): void
    {
        Assert::nullOrStringNotEmpty($postalCode);
        $this->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getRoomFiasId(): ?string
    {
        return $this->roomFiasId;
    }

    /**
     * @param string|null $roomFiasId
     */
    public function setRoomFiasId(?string $roomFiasId): void
    {
        Assert::nullOrStringNotEmpty($roomFiasId);
        $this->roomFiasId = $roomFiasId;
    }

    /**
     * @return string|null
     */
    public function getRoomType(): ?string
    {
        return $this->roomType;
    }

    /**
     * @param string|null $roomType
     */
    public function setRoomType(?string $roomType): void
    {
        Assert::nullOrStringNotEmpty($roomType);
        $this->roomType = $roomType;
    }

    /**
     * @return string|null
     */
    public function getRoomTypeFull(): ?string
    {
        return $this->roomTypeFull;
    }

    /**
     * @param string|null $roomTypeFull
     */
    public function setRoomTypeFull(?string $roomTypeFull): void
    {
        Assert::nullOrStringNotEmpty($roomTypeFull);
        $this->roomTypeFull = $roomTypeFull;
    }

    /**
     * @return string|null
     */
    public function getRoom(): ?string
    {
        return $this->room;
    }

    /**
     * @param string|null $room
     */
    public function setRoom(?string $room): void
    {
        Assert::nullOrStringNotEmpty($room);
        $this->room = $room;
    }

    /**
     * @return array|null
     */
    public function getSynonyms(): ?array
    {
        return $this->synonyms;
    }

    /**
     * @param array|null $synonyms
     */
    public function setSynonyms(?array $synonyms): void
    {
        Assert::nullOrNotEmpty($synonyms);
        $this->synonyms = $synonyms;
    }

    /**
     * @return array|null
     */
    public function getRenaming(): ?array
    {
        return $this->renaming;
    }

    /**
     * @param array|null $renaming
     */
    public function setRenaming(?array $renaming): void
    {
        Assert::nullOrNotEmpty($renaming);
        $this->renaming = $renaming;
    }

    /**
     * @return string|null
     */
    public function getTerritoryFiasId(): ?string
    {
        return $this->territoryFiasId;
    }

    /**
     * @param string|null $territoryFiasId
     */
    public function setTerritoryFiasId(?string $territoryFiasId): void
    {
        Assert::nullOrStringNotEmpty($territoryFiasId);
        $this->territoryFiasId = $territoryFiasId;
    }

    /**
     * @return string|null
     */
    public function getTerritoryKladrId(): ?string
    {
        return $this->territoryKladrId;
    }

    /**
     * @param string|null $territoryKladrId
     */
    public function setTerritoryKladrId(?string $territoryKladrId): void
    {
        Assert::nullOrStringNotEmpty($territoryKladrId);
        $this->territoryKladrId = $territoryKladrId;
    }

    /**
     * @return string|null
     */
    public function getTerritoryType(): ?string
    {
        return $this->territoryType;
    }

    /**
     * @param string|null $territoryType
     */
    public function setTerritoryType(?string $territoryType): void
    {
        Assert::nullOrStringNotEmpty($territoryType);
        $this->territoryType = $territoryType;
    }

    /**
     * @return string|null
     */
    public function getTerritoryTypeFull(): ?string
    {
        return $this->territoryTypeFull;
    }

    /**
     * @param string|null $territoryTypeFull
     */
    public function setTerritoryTypeFull(?string $territoryTypeFull): void
    {
        Assert::nullOrStringNotEmpty($territoryTypeFull);
        $this->territoryTypeFull = $territoryTypeFull;
    }

    /**
     * @return string|null
     */
    public function getTerritory(): ?string
    {
        return $this->territory;
    }

    /**
     * @param string|null $territory
     */
    public function setTerritory(?string $territory): void
    {
        Assert::nullOrStringNotEmpty($territory);
        $this->territory = $territory;
    }

    /**
     * @return string
     */
    public function getRegionWithType(): string
    {
        return $this->regionWithType;
    }

    /**
     * @param string $regionWithType
     */
    public function setRegionWithType(string $regionWithType): void
    {
        Assert::stringNotEmpty($regionWithType);
        $this->regionWithType = $regionWithType;
    }

    /**
     * @return string|null
     */
    public function getAreaWithType(): ?string
    {
        return $this->areaWithType;
    }

    /**
     * @param string|null $areaWithType
     */
    public function setAreaWithType(?string $areaWithType): void
    {
        Assert::nullOrStringNotEmpty($areaWithType);
        $this->areaWithType = $areaWithType;
    }

    /**
     * @return string|null
     */
    public function getCityWithType(): ?string
    {
        return $this->cityWithType;
    }

    /**
     * @param string|null $cityWithType
     */
    public function setCityWithType(?string $cityWithType): void
    {
        Assert::nullOrStringNotEmpty($cityWithType);
        $this->cityWithType = $cityWithType;
    }

    /**
     * @return string|null
     */
    public function getSettlementWithType(): ?string
    {
        return $this->settlementWithType;
    }

    /**
     * @param string|null $settlementWithType
     */
    public function setSettlementWithType(?string $settlementWithType): void
    {
        Assert::nullOrStringNotEmpty($settlementWithType);
        $this->settlementWithType = $settlementWithType;
    }

    /**
     * @return string|null
     */
    public function getTerritoryWithType(): ?string
    {
        return $this->territoryWithType;
    }

    /**
     * @param string|null $territoryWithType
     */
    public function setTerritoryWithType(?string $territoryWithType): void
    {
        Assert::nullOrStringNotEmpty($territoryWithType);
        $this->territoryWithType = $territoryWithType;
    }

    /**
     * @return string|null
     */
    public function getStreetWithType(): ?string
    {
        return $this->streetWithType;
    }

    /**
     * @param string|null $streetWithType
     */
    public function setStreetWithType(?string $streetWithType): void
    {
        Assert::nullOrStringNotEmpty($streetWithType);
        $this->streetWithType = $streetWithType;
    }

    /**
     * @return array
     */
    public function getLocation(): array
    {
        return $this->location;
    }

    /**
     * @param array $location
     */
    public function setLocation(array $location): void
    {
        Assert::nullOrNotEmpty($location);

        if (null !== $location) {
            Assert::isList($location);
            Assert::count($location, 2);
        }

        $this->location = $location;
    }
}
