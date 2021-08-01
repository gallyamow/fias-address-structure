<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Tests;

use CoreExtensions\AddressRepository\LevelName;
use CoreExtensions\AddressRepository\LevelNameNormalizer;
use PHPUnit\Framework\TestCase;

class LevelNameNormalizerTest extends TestCase
{
    private LevelNameNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new LevelNameNormalizer();
    }

    /**
     * @test
     */
    public function itDoesNotRecognize(): void
    {
        $this->assertEquals(
            new LevelName('UNDEFINED', 'UNDEFINED'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'UNDEFINED'))
        );
    }

    /**
     * @test
     */
    public function itIsNotCaseSensitive(): void
    {
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('РеСпУблиКа', 'реСП'))
        );
        $this->assertEquals(
            new LevelName('улица', 'ул.'),
            $this->normalizer->normalize(new LevelName('улица', 'УЛ'))
        );
    }

    /**
     * @test
     */
    public function itCorrectlyNormalizesApartByName(): void
    {
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('респУБлика', 'UNDEFINED'))
        );
        $this->assertEquals(
            new LevelName('улица', 'ул.'),
            $this->normalizer->normalize(new LevelName('УлицА', 'UNDEFINED'))
        );
    }

    /**
     * @test
     */
    public function itCorrectlyNormalizesApartByShortName(): void
    {
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'респ.'))
        );
        $this->assertEquals(
            new LevelName('улица', 'ул.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'ул.'))
        );
    }


    /**
     * @test
     */
    public function itCorrectlyNormalizesFiasLevels(): void
    {
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'респ.'))
        );
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'респ'))
        );

        $this->assertEquals(
            new LevelName('район', 'р-н'),
            $this->normalizer->normalize(new LevelName('район', 'р-н'))
        );
        $this->assertEquals(
            new LevelName('район', 'р-н'),
            $this->normalizer->normalize(new LevelName('район', 'район'))
        );

        $this->assertEquals(
            new LevelName('город', 'г.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'г'))
        );
        $this->assertEquals(
            new LevelName('город', 'г.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'г.'))
        );

        $this->assertEquals(
            new LevelName('поселок', 'п.'),
            $this->normalizer->normalize(new LevelName('поселок', 'п.'))
        );
        $this->assertEquals(
            new LevelName('поселок', 'п.'),
            $this->normalizer->normalize(new LevelName('поселок', 'п'))
        );

        $this->assertEquals(
            new LevelName('деревня', 'дер.'),
            $this->normalizer->normalize(new LevelName('деревня', 'д.'))
        );
        $this->assertEquals(
            new LevelName('деревня', 'дер.'),
            $this->normalizer->normalize(new LevelName('деревня', 'д'))
        );

        $this->assertEquals(
            new LevelName('улица', 'ул.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'ул.'))
        );
        $this->assertEquals(
            new LevelName('улица', 'ул.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'ул'))
        );

        $this->assertEquals(
            new LevelName('аллея', 'ал.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'ал'))
        );
        $this->assertEquals(
            new LevelName('аллея', 'ал.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'аллея'))
        );

        $this->assertEquals(
            new LevelName('дом', 'д.'),
            $this->normalizer->normalize(new LevelName('дом', 'д.'))
        );
        $this->assertEquals(
            new LevelName('дом', 'д.'),
            $this->normalizer->normalize(new LevelName('дом', 'д'))
        );

        $this->assertEquals(
            new LevelName('квартира', 'кв.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'кв.'))
        );
        $this->assertEquals(
            new LevelName('квартира', 'кв.'),
            $this->normalizer->normalize(new LevelName('UNDEFINED', 'кв'))
        );
    }

    /**
     * @test
     */
    public function itCorrectlyNormalizesChuvashia(): void
    {
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('Чувашия', 'Чувашия'))
        );
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('ЧувАшия', 'чувашия'))
        );
        $this->assertEquals(
            new LevelName('республика', 'респ.'),
            $this->normalizer->normalize(new LevelName('респ', 'Чувашия'))
        );
    }
}
