<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Tests\Fias;

use Addresser\FiasAddressStructure\Fias\RegionNameNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RegionNameNormalizerTest extends TestCase
{
    private RegionNameNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new RegionNameNormalizer();
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
    public function itShouldNormalizeSome(): void
    {
        $this->assertEquals('Чувашская Республика', $this->normalizer->normalize('Чувашская Республика -'));
    }

    /**
     * @test
     */
    public function itShouldSkipOthers(): void
    {
        $this->assertEquals('Башкортостан', $this->normalizer->normalize('Башкортостан'));
        $this->assertEquals('Самарская', $this->normalizer->normalize('Самарская'));
    }
}
