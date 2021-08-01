<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Tests\Fias\LevelNameResolvers;

use CoreExtensions\AddressRepository\Exceptions\LevelNameNotFoundException;
use CoreExtensions\AddressRepository\Fias\FiasLevelNameResolverInterface;
use CoreExtensions\AddressRepository\Fias\LevelNameResolvers\FiasRoomLevelNameResolver;
use CoreExtensions\AddressRepository\LevelName;
use PHPUnit\Framework\TestCase;

class FiasRoomLevelNameResolverTest extends TestCase
{
    private FiasLevelNameResolverInterface $resolver;

    protected function setUp(): void
    {
        $this->resolver = new FiasRoomLevelNameResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(LevelNameNotFoundException::class);
        $this->assertEquals(
            new LevelName('литера', 'лит.'),
            $this->resolver->resolve(50000)
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesNormalizedTypes(): void
    {
        $this->assertEquals(
            new LevelName('комната', 'комн.'),
            $this->resolver->resolve(1)
        );
        $this->assertEquals(
            new LevelName('помещение', 'пом.'),
            $this->resolver->resolve(2)
        );
    }
}
