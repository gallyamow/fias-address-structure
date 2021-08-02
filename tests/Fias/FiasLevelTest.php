<?php
declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Fias\FiasLevel;
use PHPUnit\Framework\TestCase;

class FiasLevelTest extends TestCase
{
    /**
     * @test
     */
    public function itCorrectlyMapsFiasLevels(): void
    {
        $this->assertEquals(
            AddressLevel::REGION,
            FiasLevel::mapToAddressLevel(FiasLevel::REGION)
        );

        $this->assertEquals(
            AddressLevel::AREA,
            FiasLevel::mapToAddressLevel(FiasLevel::ADMINISTRATIVE_REGION)
        );
        $this->assertEquals(
            AddressLevel::AREA,
            FiasLevel::mapToAddressLevel(FiasLevel::MUNICIPAL_DISTRICT)
        );

        $this->assertEquals(
            AddressLevel::CITY,
            FiasLevel::mapToAddressLevel(FiasLevel::RURAL_URBAN_SETTLEMENT)
        );
        $this->assertEquals(
            AddressLevel::CITY,
            FiasLevel::mapToAddressLevel(FiasLevel::CITY)
        );
        $this->assertEquals(
            AddressLevel::SETTLEMENT,
            FiasLevel::mapToAddressLevel(FiasLevel::SETTLEMENT)
        );

        $this->assertEquals(
            AddressLevel::SETTLEMENT,
            FiasLevel::mapToAddressLevel(FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE)
        );
        $this->assertEquals(
            AddressLevel::SETTLEMENT,
            FiasLevel::mapToAddressLevel(FiasLevel::INTRACITY_LEVEL)
        );

        $this->assertEquals(
            AddressLevel::STREET,
            FiasLevel::mapToAddressLevel(FiasLevel::ROAD_NETWORK_ELEMENT)
        );
        $this->assertEquals(
            AddressLevel::STREET,
            FiasLevel::mapToAddressLevel(FiasLevel::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES)
        );

        $this->assertEquals(
            AddressLevel::STEAD,
            FiasLevel::mapToAddressLevel(FiasLevel::STEAD)
        );

        $this->assertEquals(
            AddressLevel::CAR_PLACE,
            FiasLevel::mapToAddressLevel(FiasLevel::CAR_PLACE)
        );

        // пока nulls
        $this->assertEquals(
            AddressLevel::HOUSE,
            FiasLevel::mapToAddressLevel(FiasLevel::BUILDING)
        );

        $this->assertEquals(
            AddressLevel::FLAT,
            FiasLevel::mapToAddressLevel(FiasLevel::PREMISES)
        );

        $this->assertEquals(
            AddressLevel::ROOM,
            FiasLevel::mapToAddressLevel(FiasLevel::PREMISES_WITHIN_THE_PREMISES)
        );

        // не понятный уровень
        $this->assertEquals(
            AddressLevel::STREET,
            FiasLevel::mapToAddressLevel(FiasLevel::COUNTY_LEVEL)
        );
    }
}
