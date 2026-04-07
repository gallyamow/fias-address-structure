<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Tests\Fias;

use Addresser\FiasAddressStructure\Fias\StreetNameNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class StreetNameNormalizerTest extends TestCase
{
    private StreetNameNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new StreetNameNormalizer();
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfNullPassed(): void
    {
        $this->assertEquals(null, $this->normalizer->normalize(null));
    }

    /**
     * @test
     */
    public function itShouldAddSpaceIfNeed(): void
    {
        $this->assertEquals('снт Раифское (Раифское СПТ)', $this->normalizer->normalize('снт Раифское(Раифское СПТ)'));
        $this->assertEquals('С. Сайдашева', $this->normalizer->normalize('С.Сайдашева'));
        $this->assertEquals('М. Джалиля', $this->normalizer->normalize('М.Джалиля'));
        $this->assertEquals('У. Валеева', $this->normalizer->normalize('У.Валеева'));
    }

    /**
     * @test
     */
    public function itShouldNotAddSpaceIfTheyExists(): void
    {
        $this->assertEquals('ГСК Луч (Советский)', $this->normalizer->normalize('ГСК Луч (Советский)'));
        $this->assertEquals('С. Сайдашева', $this->normalizer->normalize('С. Сайдашева'));
        $this->assertEquals('М. Джалиля', $this->normalizer->normalize('М. Джалиля'));
        $this->assertEquals('У. Валеева', $this->normalizer->normalize('У. Валеева'));
    }

    /**
     * @test
     */
    public function itShouldCollapseDoubleSpace(): void
    {
        $this->assertEquals('С. Сайдашева', $this->normalizer->normalize('С.  Сайдашева'));
        $this->assertEquals('М. Джалиля', $this->normalizer->normalize('М.   Джалиля'));
        $this->assertEquals('У. Валеева', $this->normalizer->normalize('У.     Валеева'));
    }
}
