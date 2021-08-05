<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Exceptions\InvalidAddressLevelException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\ObjectAddressLevelSpecResolver;
use PHPUnit\Framework\TestCase;

class ObjectAddressLevelSpecResolverTest extends TestCase
{
    private ObjectAddressLevelSpecResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ObjectAddressLevelSpecResolver();
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenHouseLevelPassed(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'дом', 'д.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::HOUSE, 'д.')
        );
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenHouseFlatPassed(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::FLAT, 'квартира', 'кв.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::FLAT, 'кв.')
        );
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenHouseRoomPassed(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'комната', 'комн.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::ROOM, 'комн.')
        );
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenCannotResolve(): void
    {
        $this->expectException(AddressLevelSpecNotFoundException::class);
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::AREA, 'Район', 'р-н.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(50, 'undefined')
        );
    }

    /**
     * @test
     */
    public function itIsCaseInsensitive(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'аллея', 'ал.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::STREET, 'ал.')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'аллея', 'ал.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::STREET, 'АЛ.')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'аллея', 'ал.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::STREET, 'Ал.')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesChuvashia(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'республика', 'респ.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(1, 'Чувашия')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesOverAllVariantsOnAllLevel(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(
                AddressLevel::SETTLEMENT, 'просека', 'просека', AddressLevelSpec::NAME_POSITION_BEFORE
            ),
            $this->resolver->resolve(AddressLevel::SETTLEMENT, 'пр-к')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'просека', 'просека', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::STREET, 'пр-к')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesPositionAfter(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(
                AddressLevel::REGION,
                'автономная область',
                'авт. обл',
                AddressLevelSpec::NAME_POSITION_AFTER
            ),
            $this->resolver->resolve(AddressLevel::REGION, 'а.обл.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'автономный округ', 'АО', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::REGION, 'АО')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'берег', 'бер.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::STREET, 'б-г')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::AREA, 'волость', 'вол.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::AREA, 'волость')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'километр', 'км', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::STREET, 'км')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'край', 'край', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::REGION, 'край')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'область', 'обл.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::REGION, 'обл.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::AREA, 'район', 'р-н', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::AREA, 'р-н')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'слобода', 'сл.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::SETTLEMENT, 'сл.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'тракт', 'тракт', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::STREET, 'тракт')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'шоссе', 'ш.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::STREET, 'ш.')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesFiasLevels(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'республика', 'респ.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::REGION, 'респ')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::AREA, 'район', 'р-н', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(AddressLevel::AREA, 'р-н')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::CITY, 'город', 'г.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::CITY, 'г.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'поселение', 'пос.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::SETTLEMENT, 'пос.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'поселок', 'п.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::SETTLEMENT, 'п.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'деревня', 'дер.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::SETTLEMENT, 'дер.')
        );


        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'улица', 'ул.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(AddressLevel::STREET, 'ул')
        );
    }
}
