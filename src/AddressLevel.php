<?php

declare(strict_types=1);

namespace Addresser\AddressRepository;

use Addresser\AddressRepository\Fias\FiasLevel;

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

    /**
     * @param int $fiasLevel
     * @return int
     */
    public static function mapFromFiasLevel(int $fiasLevel): ?int
    {
        switch ($fiasLevel) {
            case FiasLevel::REGION:
                return self::REGION;
            case FiasLevel::ADMINISTRATIVE_REGION: // р-н Янаульский
            case FiasLevel::MUNICIPAL_DISTRICT: // м.р-н Янаульский
                return self::AREA;
            case FiasLevel::RURAL_URBAN_SETTLEMENT: // с.п. Старотимошкинское
            case FiasLevel::CITY: // г. Нефтегорск, г. Болгар, с/п Асановское, с/с Юматовский
                return self::CITY;
            case FiasLevel::SETTLEMENT: // п Краный Яр, тер Мечта, ж/д_ст Ардаши, высел Ахмасиха
            case FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE: // снт Импульс/Станкозавод, тер гк т-14
            case FiasLevel::INTRACITY_LEVEL: // р-н ЖБИ, р-н Советский
            case FiasLevel::ADDITIONAL_TERRITORIES_LEVEL: // гск Колесо, гск Восход
                return self::SETTLEMENT;
            case FiasLevel::ROAD_NETWORK_ELEMENT: // ул Привокзальная, пер Центральный
            case FiasLevel::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES: // ул 11 Линия, а/я Рябиновая
                return self::STREET;
            case FiasLevel::STEAD: // нет
                return self::STEAD;
            case FiasLevel::CAR_PLACE: // нет
                return self::CAR_PLACE;
            case FiasLevel::BUILDING: // записей в addr_obj нет, FiasAddressBuilder делает вывод на основе relation_type
                return self::HOUSE;
            case FiasLevel::PREMISES: // записей в addr_obj нет, FiasAddressBuilder делает вывод на основе relation_type
                return self::FLAT;
            case FiasLevel::PREMISES_WITHIN_THE_PREMISES: // нет
                return self::ROOM;
            case FiasLevel::COUNTY_LEVEL: // нет
                return null;
        }
    }

    // обратный mapping нельзя сделать, так как теряется некоторая информация
}
