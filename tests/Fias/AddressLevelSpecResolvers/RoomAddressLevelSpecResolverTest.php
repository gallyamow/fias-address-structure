<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\LevelNameSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\RoomAddressLevelSpecResolver;
use PHPUnit\Framework\TestCase;

class RoomAddressLevelSpecResolverTest extends TestCase
{
    private AddressLevelSpecResolverInterface $resolver;

    protected function setUp(): void
    {
        $this->resolver = new RoomAddressLevelSpecResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(LevelNameSpecNotFoundException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'UNDEFINED', 'undefined', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 50000)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesTypes(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'комната', 'комн.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 1)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'помещение', 'пом.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 2)
        );
    }
}
