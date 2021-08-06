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
}
