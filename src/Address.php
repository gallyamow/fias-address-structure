<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository;

/**
 * Плоская структура для хранения адреса.
 * Одна и та же структура будет использована как для хранения информации до районов, так и для хранения
 * информации до квартир.
 * Это представление удобно для обработки через elasticsearch.
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

    // регион
    private string $regionFiasId;
    private ?string $regionKladrId;
    private string $regionType;
    private string $regionTypeFull;
    private string $region;

    private ?string $kladrId = null;
    private ?string $okato = null;
    private ?string $oktmo = null;
    private ?string $postalCode = null;

    // район
    private ?string $areaFiasId = null;
    private ?string $areaKladrId = null;
    private ?string $areaType = null;
    private ?string $areaTypeFull = null;
    private ?string $area = null;

    // город
    private ?string $cityFiasId = null;
    private ?string $cityKladrId = null;
    private ?string $cityType = null;
    private ?string $cityTypeFull = null;
    private ?string $city = null;

    // населенный пункт
    private ?string $settlementFiasId = null;
    private ?string $settlementKladrId = null;
    private ?string $settlementType = null;
    private ?string $settlementTypeFull = null;
    private ?string $settlement = null;

    // улица
    private ?string $streetFiasId = null;
    private ?string $streetKladrId = null;
    private ?string $streetType = null;
    private ?string $streetTypeFull = null;
    private ?string $street = null;

    // дом
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

    // квартира
    private ?string $flatFiasId = null;
    private ?string $flatType = null;
    private ?string $flatTypeFull = null;
    private ?string $flat = null;

    // квартира
    private ?string $roomFiasId = null;
    private ?string $roomType = null;
    private ?string $roomTypeFull = null;
    private ?string $room = null;

    /**
     * Синонимы - различные сокращенные названия вида Башкирия, Татария, ХМАО.
     * @var array
     */
    private array $synonyms = [];

    /**
     * Старые названия - переименования регионов, городов, улиц.
     * Запоняться будет только на необходимом уровне. Для дочерних - не будет.
     * @var array
     */
    private array $renaming = [];

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getCompleteShortAddress(): string
    {
        // TODO: область идет в начале
        $chunks = [
            implode(' ', [$this->getRegionType(), $this->getRegion()]),
        ];

        if (null !== $this->getArea()) {
            $chunks[] = implode(' ', [$this->getArea(), $this->getAreaType()]);
        }

        if (null !== $this->getCity()) {
            $chunks[] = implode(' ', [$this->getCityType(), $this->getCity()]);
        }

        if (null !== $this->getSettlement()) {
            $chunks[] = implode(' ', [$this->getSettlementType(), $this->getSettlement()]);
        }

        if (null !== $this->getStreet()) {
            $chunks[] = implode(' ', [$this->getStreetType(), $this->getStreet()]);
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
            $chunks[] = '(бывш. ' . implode(', ', $this->getRenaming()) . ')';
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
        $this->room = $room;
    }

    /**
     * @return array
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * @param array $synonyms
     */
    public function setSynonyms(array $synonyms): void
    {
        $this->synonyms = $synonyms;
    }

    /**
     * @return array
     */
    public function getRenaming(): array
    {
        return $this->renaming;
    }

    /**
     * @param array $renaming
     */
    public function setRenaming(array $renaming): void
    {
        $this->renaming = $renaming;
    }
}
