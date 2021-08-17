<?php

declare(strict_types=1);

namespace Addresser\AddressRepository;

use Addresser\AddressRepository\Exceptions\InvalidAddressLevelException;

/**
 * Более привычные в быту упрощенные уровни.
 */
final class AddressLevel
{
    /**
     * регион
     */
    public const REGION = 10;

    /**
     * район в регионе
     */
    public const AREA = 20;

    /**
     * город
     */
    public const CITY = 31;

    /**
     * Поселение (внутри города, внутри районов).
     */
    public const SETTLEMENT = 32;

    /**
     * Территория (внутри города, поселения, района).
     *
     * На этот уровень происходит отображение сразу 3 уровней ФИАС: ELEMENT_OF_THE_PLANNING_STRUCTURE,
     * INTRACITY_LEVEL, ADDITIONAL_TERRITORIES_LEVEL.
     *
     * @see \Addresser\AddressRepository\Fias\FiasLevel::mapAdmHierarchyToAddressLevel
     */
    public const TERRITORY = 33;

    /**
     * улица
     */
    public const STREET = 40;

    /**
     * дом
     */
    public const HOUSE = 51;

    /**
     * участок
     */
    public const STEAD = 52;

    /**
     * квартира
     */
    public const FLAT = 61;

    /**
     * машиноместо
     */
    public const CAR_PLACE = 62;

    /**
     * комната, помещение
     */
    public const ROOM = 70;

    /**
     * @param int $addressLevel
     * @return int[]
     */
    public static function getTree(int $addressLevel): array
    {
        switch ($addressLevel) {
            case self::REGION:
                return [$addressLevel];
            case self::AREA:
                return [self::REGION, $addressLevel];
            case self::CITY:
                return [self::REGION, self::AREA, $addressLevel];
            // поселение так же может быть и внутри района и внутри города
            case self::SETTLEMENT:
                return [self::REGION, self::AREA, self::CITY, $addressLevel];
            // поселение так же может быть и внутри района и внутри города
            case self::TERRITORY:
                return [self::REGION, self::AREA, self::CITY, self::SETTLEMENT, $addressLevel];
            case self::STREET:
                return [self::REGION, self::AREA, self::CITY, self::SETTLEMENT, self::TERRITORY, $addressLevel];
            case self::HOUSE:
            case self::STEAD:
                return [
                    self::REGION,
                    self::AREA,
                    self::CITY,
                    self::SETTLEMENT,
                    self::TERRITORY,
                    self::STREET,
                    $addressLevel,
                ];
            case self::FLAT:
            case self::CAR_PLACE:
                return [
                    self::REGION,
                    self::AREA,
                    self::CITY,
                    self::SETTLEMENT,
                    self::TERRITORY,
                    self::STREET,
                    self::HOUSE,
                    $addressLevel,
                ];
            case self::ROOM:
                return [
                    self::REGION,
                    self::AREA,
                    self::CITY,
                    self::SETTLEMENT,
                    self::TERRITORY,
                    self::STREET,
                    self::HOUSE,
                    self::FLAT,
                    $addressLevel,
                ];
        }

        throw new InvalidAddressLevelException('Invalid addressLevel im method getParentLevels.');
    }
}
