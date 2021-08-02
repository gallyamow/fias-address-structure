<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevel;

class FiasLevel
{
    /**
     * Субъект РФ
     */
    public const REGION = 1;

    /**
     * Административный район
     * (р-н Янаульский)
     */
    public const ADMINISTRATIVE_REGION = 2;

    /**
     * Муниципальный район
     * (м.р-н Янаульский)
     */
    public const MUNICIPAL_DISTRICT = 3;

    /**
     * Сельское/городское поселение
     * (с.п. Старотимошкинское)
     */
    public const RURAL_URBAN_SETTLEMENT = 4;

    /**
     * Город
     * (г. Нефтегорск, г. Болгар, с/п Асановское, с/с Юматовский)
     */
    public const CITY = 5;

    /**
     * Населенный пункт
     * (п Краный Яр, тер Мечта, ж/д_ст Ардаши, высел Ахмасиха)
     */
    public const SETTLEMENT = 6;

    /**
     * Элемент планировочной структуры
     * (снт Импульс/Станкозавод, тер гк т-14)
     */
    public const ELEMENT_OF_THE_PLANNING_STRUCTURE = 7;

    /**
     * Элемент улично-дорожной сети
     * (ул Привокзальная, пер Центральный)
     */
    public const ROAD_NETWORK_ELEMENT = 8;

    /**
     * Земельный участок
     */
    public const STEAD = 9;

    /**
     * Здание (сооружение)
     */
    public const BUILDING = 10;

    /**
     * Помещение
     */
    public const PREMISES = 11;

    /**
     * Помещения в пределах помещения
     */
    public const PREMISES_WITHIN_THE_PREMISES = 12;

    /**
     * Уровень автономного округа (устаревшее)
     */
    public const COUNTY_LEVEL = 13;

    /**
     * Уровень внутригородской территории (устаревшее)
     * (р-н ЖБИ, р-н Советский)
     */
    public const INTRACITY_LEVEL = 14;

    /**
     * Уровень дополнительных территорий (устаревшее)
     * (гск Колесо, гск Восход)
     */
    public const ADDITIONAL_TERRITORIES_LEVEL = 15;

    /**
     * Уровень объектов на дополнительных территориях (устаревшее)
     * (ул 11 Линия, а/я Рябиновая)
     */
    public const OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES = 16;

    /**
     * Машино-место
     */
    public const CAR_PLACE = 17;

    /**
     * @param int $fiasLevel
     * @return int
     */
    public static function mapToAddressLevel(int $fiasLevel): ?int
    {
        // обратный mapping нельзя сделать, так как теряется некоторая информация
        switch ($fiasLevel) {
            case self::REGION:
                return AddressLevel::REGION;
            case self::ADMINISTRATIVE_REGION: // р-н Янаульский
            case self::MUNICIPAL_DISTRICT: // м.р-н Янаульский
                return AddressLevel::AREA;
            case self::RURAL_URBAN_SETTLEMENT: // с.п. Старотимошкинское
            case self::CITY: // г. Нефтегорск, г. Болгар, с/п Асановское, с/с Юматовский
                return AddressLevel::CITY;
            case self::SETTLEMENT: // п Краный Яр, тер Мечта, ж/д_ст Ардаши, высел Ахмасиха
            case self::ELEMENT_OF_THE_PLANNING_STRUCTURE: // снт Импульс/Станкозавод, тер гк т-14
            case self::INTRACITY_LEVEL: // р-н ЖБИ, р-н Советский
            case self::ADDITIONAL_TERRITORIES_LEVEL: // гск Колесо, гск Восход
                return AddressLevel::SETTLEMENT;
            case self::ROAD_NETWORK_ELEMENT: // ул Привокзальная, пер Центральный
            case self::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES: // ул 11 Линия, а/я Рябиновая
            case self::COUNTY_LEVEL: // нет
                return AddressLevel::STREET;
            case self::STEAD: // нет
                return AddressLevel::STEAD;
            case self::CAR_PLACE: // нет
                return AddressLevel::CAR_PLACE;
            case self::BUILDING: // записей в addr_obj нет, FiasAddressBuilder делает вывод на основе relation_type
                return AddressLevel::HOUSE;
            case self::PREMISES: // записей в addr_obj нет, FiasAddressBuilder делает вывод на основе relation_type
                return AddressLevel::FLAT;
            case self::PREMISES_WITHIN_THE_PREMISES: // нет
                return AddressLevel::ROOM;
        }
    }
}
