<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\HouseAddressLevelSpecResolver;
use PHPUnit\Framework\TestCase;

class HouseAddressLevelSpecResolverTest extends TestCase
{
    private HouseAddressLevelSpecResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new HouseAddressLevelSpecResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(AddressLevelSpecNotFoundException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'UNDEFINED', 'undefined', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(50000)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesTypes(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'литера', 'лит.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(9)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'корпус', 'корп.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(10)
        );
    }
}
