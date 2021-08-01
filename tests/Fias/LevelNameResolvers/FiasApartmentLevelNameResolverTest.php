<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\LevelNameResolvers;

use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\FiasLevelNameResolverInterface;
use Addresser\AddressRepository\Fias\LevelNameResolvers\FiasApartmentLevelNameResolver;
use Addresser\AddressRepository\LevelName;
use PHPUnit\Framework\TestCase;

class FiasApartmentLevelNameResolverTest extends TestCase
{
    private FiasLevelNameResolverInterface $resolver;

    protected function setUp(): void
    {
        $this->resolver = new FiasApartmentLevelNameResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(LevelNameNotFoundException::class);
        $this->assertEquals(
            new LevelName('комната', 'комн.'),
            $this->resolver->resolve(50000)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesNormalizedTypes(): void
    {
        $this->assertEquals(
            new LevelName('помещение', 'пом.'),
            $this->resolver->resolve(1)
        );
        $this->assertEquals(
            new LevelName('комната', 'комн.'),
            $this->resolver->resolve(4)
        );
        $this->assertEquals(
            new LevelName('погреб', 'погр.'),
            $this->resolver->resolve(12)
        );
        $this->assertEquals(
            new LevelName('гараж', 'гар.'),
            $this->resolver->resolve(13)
        );
    }
}
