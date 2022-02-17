<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\AddHouseAddressLevelSpecResolver;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AddHouseAddressLevelSpecResolverTest extends TestCase
{
    private AddHouseAddressLevelSpecResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new AddHouseAddressLevelSpecResolver();
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
            $this->resolver->resolve(4)
        );
    }
}
