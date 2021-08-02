<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\LevelNameSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\HouseAddressLevelSpecResolver;
use Addresser\AddressRepository\AddressLevelSpec;
use PHPUnit\Framework\TestCase;

class HouseAddressLevelSpecResolverTest extends TestCase
{
    private AddressLevelSpecResolverInterface $resolver;

    protected function setUp(): void
    {
        $this->resolver = new HouseAddressLevelSpecResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(LevelNameSpecNotFoundException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'UNDEFINED', 'undefined', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::HOUSE, 50000)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesTypes(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'литера', 'лит.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::HOUSE, 9)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'корпус', 'корп.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::HOUSE, 10)
        );
    }
}
