<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\LevelNameSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\ApartmentAddressLevelSpecResolver;
use Addresser\AddressRepository\AddressLevelSpec;
use PHPUnit\Framework\TestCase;

class ApartmentAddressLevelSpecResolverTest extends TestCase
{
    private AddressLevelSpecResolverInterface $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ApartmentAddressLevelSpecResolver();
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
            new AddressLevelSpec(AddressLevel::ROOM, 'помещение', 'пом.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 1)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'комната', 'комн.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 4)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'погреб', 'погр.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 12)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'гараж', 'гар.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 13)
        );
    }
}
