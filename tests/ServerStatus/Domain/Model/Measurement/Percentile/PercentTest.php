<?php
declare(strict_types=1);

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Domain\Model\Measurement\Percentile;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;

class PercentTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldZeroAsDefaultValue()
    {
        $percent = PercentDataBuilder::aPercent()->build();

        $this->assertEquals(0, $percent->decimal());
        $this->assertEquals(0, $percent->percentage());
    }

    /**
     * @test
     */
    public function itShouldReturnDefinedValue()
    {
        $percent = PercentDataBuilder::aPercent()->withValue(0.123456)->build();

        $this->assertEquals(0.123456, $percent->decimal());
        $this->assertEquals(12.3456, $percent->percentage());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionForNegativeValues()
    {
        PercentDataBuilder::aPercent()->withValue(-0.123456)->build();
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionForValuesGreaterThanOne()
    {
        PercentDataBuilder::aPercent()->withValue(1.001)->build();
    }

    /**
     * @test
     */
    public function itShouldReturnFormattedValueWhenCastedToString()
    {
        $this->assertEquals("25.11%", (string) PercentDataBuilder::aPercent()->withValue(0.2511499)->build());
        $this->assertEquals("25.12%", (string) PercentDataBuilder::aPercent()->withValue(0.2511500)->build());
    }

    /**
     * @test
     */
    public function itShouldBeCreatedFromDecimalFraction()
    {
        $this->assertEquals(0.12345, Percent::createFromDecimalFraction(0.12345)->decimal());
    }

    /**
     * @test
     */
    public function itShouldBeCreatedFromPercentageValue()
    {
        $this->assertEquals(0.12345, Percent::createFromPercentage(12.345)->decimal());
    }
}
