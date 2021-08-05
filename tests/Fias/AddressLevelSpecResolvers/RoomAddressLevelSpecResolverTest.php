<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\RoomAddressLevelSpecResolver;
use PHPUnit\Framework\TestCase;

class RoomAddressLevelSpecResolverTest extends TestCase
{
    private RoomAddressLevelSpecResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new RoomAddressLevelSpecResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(AddressLevelSpecNotFoundException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'UNDEFINED', 'undefined', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(50000)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesTypes(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'комната', 'комн.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(1)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'помещение', 'пом.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(2)
        );
    }
}
