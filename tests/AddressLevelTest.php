<?php
declare(strict_types=1);

namespace Addresser\AddressRepository\Tests;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Fias\FiasLevel;
use PHPUnit\Framework\TestCase;

class AddressLevelTest extends TestCase
{
    /**
     * @test
     */
    public function itCorrectlyMapsFiasLevels(): void
    {
        $this->assertEquals(
            AddressLevel::REGION,
            AddressLevel::mapFromFiasLevel(FiasLevel::REGION)
        );

        $this->assertEquals(
            AddressLevel::AREA,
            AddressLevel::mapFromFiasLevel(FiasLevel::ADMINISTRATIVE_REGION)
        );
        $this->assertEquals(
            AddressLevel::AREA,
            AddressLevel::mapFromFiasLevel(FiasLevel::MUNICIPAL_DISTRICT)
        );

        $this->assertEquals(
            AddressLevel::CITY,
            AddressLevel::mapFromFiasLevel(FiasLevel::RURAL_URBAN_SETTLEMENT)
        );
        $this->assertEquals(
            AddressLevel::CITY,
            AddressLevel::mapFromFiasLevel(FiasLevel::CITY)
        );
        $this->assertEquals(
            AddressLevel::SETTLEMENT,
            AddressLevel::mapFromFiasLevel(FiasLevel::SETTLEMENT)
        );

        $this->assertEquals(
            AddressLevel::SETTLEMENT,
            AddressLevel::mapFromFiasLevel(FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE)
        );
        $this->assertEquals(
            AddressLevel::SETTLEMENT,
            AddressLevel::mapFromFiasLevel(FiasLevel::INTRACITY_LEVEL)
        );

        $this->assertEquals(
            AddressLevel::STREET,
            AddressLevel::mapFromFiasLevel(FiasLevel::ROAD_NETWORK_ELEMENT)
        );
        $this->assertEquals(
            AddressLevel::STREET,
            AddressLevel::mapFromFiasLevel(FiasLevel::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES)
        );

        $this->assertEquals(
            AddressLevel::STEAD,
            AddressLevel::mapFromFiasLevel(FiasLevel::STEAD)
        );

        $this->assertEquals(
            AddressLevel::CAR_PLACE,
            AddressLevel::mapFromFiasLevel(FiasLevel::CAR_PLACE)
        );

        // пока nulls
        $this->assertEquals(
            AddressLevel::HOUSE,
            AddressLevel::mapFromFiasLevel(FiasLevel::BUILDING)
        );

        $this->assertEquals(
            AddressLevel::FLAT,
            AddressLevel::mapFromFiasLevel(FiasLevel::PREMISES)
        );

        $this->assertEquals(
            AddressLevel::ROOM,
            AddressLevel::mapFromFiasLevel(FiasLevel::PREMISES_WITHIN_THE_PREMISES)
        );

        // не понятный уровень
        $this->assertEquals(
            null,
            AddressLevel::mapFromFiasLevel(FiasLevel::COUNTY_LEVEL)
        );
    }
}
