<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests;

use Addresser\AddressRepository\AddressLevel;
use PHPUnit\Framework\TestCase;

class AddressLevelTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsCorrectTree(): void
    {
        $this->assertEquals(
            [
                AddressLevel::REGION,
            ],
            AddressLevel::getTree(AddressLevel::REGION)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
            ],
            AddressLevel::getTree(AddressLevel::AREA)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
            ],
            AddressLevel::getTree(AddressLevel::CITY)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
            ],
            AddressLevel::getTree(AddressLevel::SETTLEMENT)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::TERRITORY,
            ],
            AddressLevel::getTree(AddressLevel::TERRITORY)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::TERRITORY,
                AddressLevel::STREET,
            ],
            AddressLevel::getTree(AddressLevel::STREET)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::TERRITORY,
                AddressLevel::STREET,
                AddressLevel::HOUSE,
            ],
            AddressLevel::getTree(AddressLevel::HOUSE)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::TERRITORY,
                AddressLevel::STREET,
                AddressLevel::HOUSE,
                AddressLevel::FLAT,
            ],
            AddressLevel::getTree(AddressLevel::FLAT)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::TERRITORY,
                AddressLevel::STREET,
                AddressLevel::HOUSE,
                AddressLevel::FLAT,
                AddressLevel::ROOM,
            ],
            AddressLevel::getTree(AddressLevel::ROOM)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::TERRITORY,
                AddressLevel::STREET,
                AddressLevel::HOUSE,
                AddressLevel::CAR_PLACE,
            ],
            AddressLevel::getTree(AddressLevel::CAR_PLACE)
        );

        $this->assertEquals(
            [
                AddressLevel::REGION,
                AddressLevel::AREA,
                AddressLevel::CITY,
                AddressLevel::SETTLEMENT,
                AddressLevel::TERRITORY,
                AddressLevel::STREET,
                AddressLevel::STEAD,
            ],
            AddressLevel::getTree(AddressLevel::STEAD)
        );
    }
}
