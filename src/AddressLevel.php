<?php

declare(strict_types=1);

namespace Addresser\AddressRepository;

/**
 * Более привычные в быту упрощенные уровни.
 */
final class AddressLevel
{
    /**
     * регион
     */
    public const REGION = 1;

    /**
     * район в регионе
     */
    public const AREA = 2;

    /**
     * город
     */
    public const CITY = 3;

    /**
     * Поселение (внутри города, внутри районов).
     * На этот уровень происходит отображение сразу 3 уровней ФИАС: SETTLEMENT, ELEMENT_OF_THE_PLANNING_STRUCTURE,
     * INTRACITY_LEVEL, ADDITIONAL_TERRITORIES_LEVEL.
     *
     * @see \Addresser\AddressRepository\Fias\FiasLevel::mapAdmHierarchyToAddressLevel
     */
    public const SETTLEMENT = 4;

    /**
     * улица
     */
    public const STREET = 5;

    /**
     * дом
     */
    public const HOUSE = 6;

    /**
     * квартира
     */
    public const FLAT = 7;

    /**
     * комната, помещение
     */
    public const ROOM = 8;

    /**
     * участок
     */
    public const STEAD = 50;

    /**
     * машиноместо
     */
    public const CAR_PLACE = 60;
}
