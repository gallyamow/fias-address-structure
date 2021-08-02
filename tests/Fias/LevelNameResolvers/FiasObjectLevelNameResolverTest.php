<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\LevelNameResolvers;

use Addresser\AddressRepository\Exceptions\LevelNameNotFoundException;
use Addresser\AddressRepository\Fias\LevelNameResolvers\FiasTypeSource;
use Addresser\AddressRepository\Fias\LevelNameResolvers\FiasObjectLevelNameResolver;
use Addresser\AddressRepository\AddressLevelSpec;
use PHPUnit\Framework\TestCase;

class FiasObjectLevelNameResolverTest extends TestCase
{
    private FiasObjectLevelNameResolver $resolver;

    protected function setUp(): void
    {
        /**
         * @var FiasTypeSource $sourceMock
         */
        $sourceMock = $this->createMock(FiasTypeSource::class);
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
            new AddressLevelSpec('Район', 'р-н'),
            $this->resolver->resolve(50, 'undefined')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesChuvashia(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('чувашия', 'чувашия'),
            $this->resolver->resolve(1, 'Чувашия')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesNormalizedTypes(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('район', 'р-н'),
            $this->resolver->resolve(2, 'р-н')
        );
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ'),
            $this->resolver->resolve(1, 'респ')
        );
    }
}
