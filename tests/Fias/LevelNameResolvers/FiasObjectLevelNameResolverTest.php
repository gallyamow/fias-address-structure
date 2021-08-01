<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Tests\Fias\LevelNameResolvers;

use CoreExtensions\AddressRepository\Exceptions\LevelNameNotFoundException;
use CoreExtensions\AddressRepository\Fias\LevelNameResolvers\FiasLevelNameSource;
use CoreExtensions\AddressRepository\Fias\LevelNameResolvers\FiasObjectLevelNameResolver;
use CoreExtensions\AddressRepository\LevelName;
use PHPUnit\Framework\TestCase;

class FiasObjectLevelNameResolverTest extends TestCase
{
    private FiasObjectLevelNameResolver $resolver;

    protected function setUp(): void
    {
        /**
         * @var FiasLevelNameSource $sourceMock
         */
        $sourceMock = $this->createMock(FiasLevelNameSource::class);
        $sourceMock->method('getItems')
            ->willReturn(
                [
                    ['level' => 1, 'shortname' => 'а.обл', 'name' => 'Автономная область'],
                    ['level' => 1, 'shortname' => 'Респ', 'name' => 'Республика'],
                    ['level' => 1, 'shortname' => 'респ.', 'name' => 'Республика'],
                    ['level' => 1, 'shortname' => 'Чувашия', 'name' => 'Чувашия'],
                    ['level' => 2, 'shortname' => 'р-н', 'name' => 'Район'],
                ]
            );

        $this->resolver = new FiasObjectLevelNameResolver($sourceMock);
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(LevelNameNotFoundException::class);
        $this->assertEquals(
            new LevelName('Район', 'р-н'),
            $this->resolver->resolve(50, 'undefined')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesChuvashia(): void
    {
        $this->assertEquals(
            new LevelName('чувашия', 'чувашия'),
            $this->resolver->resolve(1, 'Чувашия')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesNormalizedTypes(): void
    {
        $this->assertEquals(
            new LevelName('район', 'р-н'),
            $this->resolver->resolve(2, 'р-н')
        );
        $this->assertEquals(
            new LevelName('республика', 'респ'),
            $this->resolver->resolve(1, 'респ')
        );
    }
}
