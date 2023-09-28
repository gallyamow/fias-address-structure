<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BaseNameNormalizerTest extends TestCase
{
    private BaseNameNormalizer $formatter;

    protected function setUp(): void
    {
        $this->formatter = new BaseNameNormalizer();
    }

    /**
     * @test
     */
    public function itShouldReturnNullIfNullPassed(): void
    {
        $this->assertEquals(null, $this->formatter->normalize(null));
    }

    /**
     * @test
     */
    public function itShouldAddSpaceIfNeed(): void
    {
        $this->assertEquals('снт Раифское (Раифское СПТ)', $this->formatter->normalize('снт Раифское(Раифское СПТ)'));
        $this->assertEquals('С. Сайдашева', $this->formatter->normalize('С.Сайдашева'));
        $this->assertEquals('М. Джалиля', $this->formatter->normalize('М.Джалиля'));
        $this->assertEquals('У. Валеева', $this->formatter->normalize('У.Валеева'));
    }

    /**
     * @test
     */
    public function itShouldNotAddSpaceIfTheyExists(): void
    {
        $this->assertEquals('ГСК Луч (Советский)', $this->formatter->normalize('ГСК Луч (Советский)'));
        $this->assertEquals('С. Сайдашева', $this->formatter->normalize('С. Сайдашева'));
        $this->assertEquals('М. Джалиля', $this->formatter->normalize('М. Джалиля'));
        $this->assertEquals('У. Валеева', $this->formatter->normalize('У. Валеева'));
    }

    /**
     * @test
     */
    public function itShouldCollapseDoubleSpace(): void
    {
        $this->assertEquals('С. Сайдашева', $this->formatter->normalize('С.  Сайдашева'));
        $this->assertEquals('М. Джалиля', $this->formatter->normalize('М.   Джалиля'));
        $this->assertEquals('У. Валеева', $this->formatter->normalize('У.     Валеева'));
    }
}
