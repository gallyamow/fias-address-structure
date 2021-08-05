<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolverInterface;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\AddHouseAddressLevelSpecResolver;
use Addresser\AddressRepository\AddressLevelSpec;
use PHPUnit\Framework\TestCase;

class AddHouseAddressLevelSpecResolverTest extends TestCase
{
    private AddressLevelSpecResolverInterface $resolver;

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
            $this->resolver->resolve(AddressLevel::HOUSE, 4)
        );
    }
}
