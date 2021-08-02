<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Tests;

use Addresser\AddressRepository\AddressLevelSpec;
use Addresser\AddressRepository\AddresLevelSpecNormalizer;
use PHPUnit\Framework\TestCase;

class LevelNameNormalizerTest extends TestCase
{
    private AddresLevelSpecNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new AddresLevelSpecNormalizer();
    }

    /**
     * @test
     */
    public function itDoesNotRecognize(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('UNDEFINED', 'UNDEFINED'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'UNDEFINED'))
        );
    }

    /**
     * @test
     */
    public function itIsNotCaseSensitive(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('РеСпУблиКа', 'реСП'))
        );
        $this->assertEquals(
            new AddressLevelSpec('улица', 'ул.'),
            $this->normalizer->normalize(new AddressLevelSpec('улица', 'УЛ'))
        );
    }

    /**
     * @test
     */
    public function itCorrectlyNormalizesApartByName(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('респУБлика', 'UNDEFINED'))
        );
        $this->assertEquals(
            new AddressLevelSpec('улица', 'ул.'),
            $this->normalizer->normalize(new AddressLevelSpec('УлицА', 'UNDEFINED'))
        );
    }

    /**
     * @test
     */
    public function itCorrectlyNormalizesApartByShortName(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'респ.'))
        );
        $this->assertEquals(
            new AddressLevelSpec('улица', 'ул.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'ул.'))
        );
    }


    /**
     * @test
     */
    public function itCorrectlyNormalizesFiasLevels(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'респ.'))
        );
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'респ'))
        );

        $this->assertEquals(
            new AddressLevelSpec('район', 'р-н'),
            $this->normalizer->normalize(new AddressLevelSpec('район', 'р-н'))
        );
        $this->assertEquals(
            new AddressLevelSpec('район', 'р-н'),
            $this->normalizer->normalize(new AddressLevelSpec('район', 'район'))
        );

        $this->assertEquals(
            new AddressLevelSpec('город', 'г.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'г'))
        );
        $this->assertEquals(
            new AddressLevelSpec('город', 'г.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'г.'))
        );

        $this->assertEquals(
            new AddressLevelSpec('поселок', 'п.'),
            $this->normalizer->normalize(new AddressLevelSpec('поселок', 'п.'))
        );
        $this->assertEquals(
            new AddressLevelSpec('поселок', 'п.'),
            $this->normalizer->normalize(new AddressLevelSpec('поселок', 'п'))
        );

        $this->assertEquals(
            new AddressLevelSpec('деревня', 'дер.'),
            $this->normalizer->normalize(new AddressLevelSpec('деревня', 'д.'))
        );
        $this->assertEquals(
            new AddressLevelSpec('деревня', 'дер.'),
            $this->normalizer->normalize(new AddressLevelSpec('деревня', 'д'))
        );

        $this->assertEquals(
            new AddressLevelSpec('улица', 'ул.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'ул.'))
        );
        $this->assertEquals(
            new AddressLevelSpec('улица', 'ул.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'ул'))
        );

        $this->assertEquals(
            new AddressLevelSpec('аллея', 'ал.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'ал'))
        );
        $this->assertEquals(
            new AddressLevelSpec('аллея', 'ал.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'аллея'))
        );

        $this->assertEquals(
            new AddressLevelSpec('дом', 'д.'),
            $this->normalizer->normalize(new AddressLevelSpec('дом', 'д.'))
        );
        $this->assertEquals(
            new AddressLevelSpec('дом', 'д.'),
            $this->normalizer->normalize(new AddressLevelSpec('дом', 'д'))
        );

        $this->assertEquals(
            new AddressLevelSpec('квартира', 'кв.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'кв.'))
        );
        $this->assertEquals(
            new AddressLevelSpec('квартира', 'кв.'),
            $this->normalizer->normalize(new AddressLevelSpec('UNDEFINED', 'кв'))
        );
    }

    /**
     * @test
     */
    public function itCorrectlyNormalizesChuvashia(): void
    {
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('Чувашия', 'Чувашия'))
        );
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('ЧувАшия', 'чувашия'))
        );
        $this->assertEquals(
            new AddressLevelSpec('республика', 'респ.'),
            $this->normalizer->normalize(new AddressLevelSpec('респ', 'Чувашия'))
        );
    }
}
