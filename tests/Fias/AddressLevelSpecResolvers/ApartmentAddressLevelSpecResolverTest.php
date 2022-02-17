<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\ApartmentAddressLevelSpecResolver;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ApartmentAddressLevelSpecResolverTest extends TestCase
{
    private ApartmentAddressLevelSpecResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ApartmentAddressLevelSpecResolver();
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
            new AddressLevelSpec(AddressLevel::FLAT, 'помещение', 'пом.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(1)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::FLAT, 'квартира', 'кв.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(2)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::FLAT, 'комната', 'комн.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(4)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::FLAT, 'погреб', 'погр.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(12)
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::FLAT, 'гараж', 'гар.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(13)
        );
    }
}
