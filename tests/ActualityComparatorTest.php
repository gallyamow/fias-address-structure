<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Tests;

use CoreExtensions\AddressRepository\ActualityComparator;
use PHPUnit\Framework\TestCase;

class ActualityComparatorTest extends TestCase
{
    private ActualityComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new ActualityComparator();
    }

    /**
     * @test
     */
    public function itCorrectlyComparesPeriods(): void
    {
        $this->assertEquals(0, $this->comparator->compare('1900-01-01', '2079-06-06', '1900-01-01', '2079-06-06'));

        $this->assertEquals(-1, $this->comparator->compare('1900-01-01', '1900-01-01', '1900-01-01', '2079-06-06'));
        $this->assertEquals(-1, $this->comparator->compare('1900-01-01', '1970-01-01', '1900-01-01', '2079-06-06'));
        $this->assertEquals(-1, $this->comparator->compare('1900-01-01', '2079-06-06', '1900-01-02', '2079-06-06'));

        $this->assertEquals(1, $this->comparator->compare('1900-01-01', '2079-06-06', '1900-01-01', '1900-01-01'));
        $this->assertEquals(1, $this->comparator->compare('1900-01-01', '2079-06-06', '1900-01-01', '1970-01-01'));
        $this->assertEquals(1, $this->comparator->compare('1900-01-02', '2079-06-06', '1900-01-01', '2079-06-06',));
    }
}
