<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests\Fias\AddressLevelSpecResolvers;

use Addresser\AddressRepository\AddressLevel;
use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\Exceptions\AddressLevelSpecNotFoundException;
use Addresser\AddressRepository\Exceptions\InvalidAddressLevelException;
use Addresser\AddressRepository\Fias\AddressLevelSpecResolvers\ObjectAddressLevelSpecResolver;
use Addresser\AddressRepository\Fias\FiasLevel;
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
    public function itShouldThrowExceptionWhenBuildingLevelPassed(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->expectExceptionMessage(
            sprintf('FiasLevel "%d" cannot be resolved by object resolver.', FiasLevel::BUILDING)
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::HOUSE, 'дом', 'д.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::BUILDING, 'д.')
        );
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenPremisesPassed(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->expectExceptionMessage(
            sprintf('FiasLevel "%d" cannot be resolved by object resolver.', FiasLevel::PREMISES)
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::FLAT, 'квартира', 'кв.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::PREMISES, 'кв.')
        );
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionWhenPremisesWithPremisesPassed(): void
    {
        $this->expectException(InvalidAddressLevelException::class);
        $this->expectExceptionMessage(
            sprintf('FiasLevel "%d" cannot be resolved by object resolver.', FiasLevel::PREMISES_WITHIN_THE_PREMISES)
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::ROOM, 'комната', 'комн.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::PREMISES_WITHIN_THE_PREMISES, 'комн.')
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
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'ал.')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'аллея', 'ал.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'АЛ.')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'аллея', 'ал.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'Ал.')
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
                AddressLevel::TERRITORY, 'просека', 'просека', AddressLevelSpec::NAME_POSITION_BEFORE
            ),
            $this->resolver->resolve(FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE, 'пр-к')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'просека', 'просека', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'пр-к')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'просека', 'просека', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES, 'пр-к')
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
            $this->resolver->resolve(FiasLevel::REGION, 'а.обл.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'автономный округ', 'АО', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::REGION, 'АО')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'берег', 'бер.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'б-г')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::AREA, 'волость', 'вол.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ADMINISTRATIVE_REGION, 'волость')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'километр', 'км', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::OBJECT_LEVEL_IN_ADDITIONAL_TERRITORIES, 'км')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'край', 'край', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::REGION, 'край')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'область', 'обл.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::REGION, 'обл.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::AREA, 'район', 'р-н', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ADMINISTRATIVE_REGION, 'р-н')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'слобода', 'сл.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::SETTLEMENT, 'сл.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'тракт', 'тракт', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'тракт')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'шоссе', 'ш.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'ш.')
        );
    }

    /**
     * @test
     */
    public function itCorrectlyResolvesFiasLevels(): void
    {
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::REGION, 'республика', 'респ.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::REGION, 'респ')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::AREA, 'район', 'р-н', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ADMINISTRATIVE_REGION, 'р-н')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::CITY, 'город', 'г.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::CITY, 'г.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'поселение', 'пос.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::SETTLEMENT, 'пос.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'поселок', 'п.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::SETTLEMENT, 'п.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::SETTLEMENT, 'деревня', 'дер.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::SETTLEMENT, 'дер.')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::STREET, 'улица', 'ул.', AddressLevelSpec::NAME_POSITION_BEFORE),
            $this->resolver->resolve(FiasLevel::ROAD_NETWORK_ELEMENT, 'ул')
        );

        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::TERRITORY, 'слобода', 'сл.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ELEMENT_OF_THE_PLANNING_STRUCTURE, 'сл.')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::TERRITORY, 'слобода', 'сл.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::INTRACITY_LEVEL, 'сл.')
        );
        $this->assertEquals(
            new AddressLevelSpec(AddressLevel::TERRITORY, 'слобода', 'сл.', AddressLevelSpec::NAME_POSITION_AFTER),
            $this->resolver->resolve(FiasLevel::ADDITIONAL_TERRITORIES_LEVEL, 'сл.')
        );
    }
}
