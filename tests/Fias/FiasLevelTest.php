<?php
declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\InvalidAddressLevelException;
use Addresser\AddressRepository\Fias\FiasLevel;
use PHPUnit\Framework\TestCase;

class FiasLevelTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldThrowExceptionWhenFiasLevelIsMunicipalDistrict(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->expectExceptionMessage(
            sprintf('Wrong fiasLevel "%d" used in administrative hierarchy.', FiasLevel::MUNICIPAL_DISTRICT)
        );

        $this->assertEquals(
            AddressLevel::CITY,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::MUNICIPAL_DISTRICT)
        );
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenFiasLevelIsRURAL_URBAN_SETTLEMENT(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->expectExceptionMessage(
            sprintf('Wrong fiasLevel "%d" used in administrative hierarchy.', FiasLevel::RURAL_URBAN_SETTLEMENT)
        );

        $this->assertEquals(
            AddressLevel::CITY,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::RURAL_URBAN_SETTLEMENT)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyMapsFiasLevels(): void
    {
        $this->assertEquals(
            AddressLevel::REGION,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::REGION)
        );

        $this->assertEquals(
            AddressLevel::AREA,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::ADMINISTRATIVE_REGION)
        );
        $this->assertEquals(
            AddressLevel::AREA,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::ADMINISTRATIVE_REGION)
        );

        $this->assertEquals(
            AddressLevel::CITY,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::CITY)
        );
        $this->assertEquals(
            AddressLevel::SETTLEMENT,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::SETTLEMENT)
        );

        $this->assertEquals(
            AddressLevel::TERRITORY,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE)
        );
        $this->assertEquals(
            AddressLevel::TERRITORY,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::INTRACITY_LEVEL)
        );
        $this->assertEquals(
            AddressLevel::TERRITORY,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::ADDITIONAL_TERRITORIES_LEVEL)
        );

        $this->assertEquals(
            AddressLevel::STREET,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::ROAD_NETWORK_ELEMENT)
        );
        $this->assertEquals(
            AddressLevel::STREET,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES)
        );

        $this->assertEquals(
            AddressLevel::STEAD,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::STEAD)
        );

        $this->assertEquals(
            AddressLevel::CAR_PLACE,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::CAR_PLACE)
        );

        // пока nulls
        $this->assertEquals(
            AddressLevel::HOUSE,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::BUILDING)
        );

        $this->assertEquals(
            AddressLevel::FLAT,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::PREMISES)
        );

        $this->assertEquals(
            AddressLevel::ROOM,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::PREMISES_WITHIN_THE_PREMISES)
        );

        // не понятный уровень
        $this->assertEquals(
            AddressLevel::STREET,
            FiasLevel::mapAdmHierarchyToAddressLevel(FiasLevel::COUNTY_LEVEL)
        );
    }
}
