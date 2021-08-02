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
     * поселение (деревни внутри города, районов)
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
