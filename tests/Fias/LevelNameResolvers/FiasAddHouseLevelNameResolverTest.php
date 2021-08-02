<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\LevelNameResolvers;

use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\FiasAddressLevelSpecResolverInterface;
use Addresser\AddressRepository\Fias\LevelNameResolvers\FiasAddHouseAddressLevelSpecResolver;
use Addresser\AddressRepository\AddressLevelSpec;
use PHPUnit\Framework\TestCase;

class FiasAddHouseLevelNameResolverTest extends TestCase
{
    private FiasAddressLevelSpecResolverInterface $resolver;

    protected function setUp(): void
    {
        $this->resolver = new FiasAddHouseAddressLevelSpecResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(LevelNameNotFoundException::class);
        $this->assertEquals(
            new AddressLevelSpec('комната', 'комн.'),
            $this->resolver->resolve(50000)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesNormalizedTypes(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('литера', 'лит.'),
            $this->resolver->resolve(4)
        );
    }
}
