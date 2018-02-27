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

class PercentileCalculatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptAnEmptyList()
    {
        $calculator = PercentileCalculatorDataBuilder::aPercentileCalculator()->withValues([])->build();
        $percentMax = PercentDataBuilder::aPercent()->withValue(1)->build();
        $expectedMax = PercentileDataBuilder::aPercentile()->withValue(0)->withPercent($percentMax)->build();

        $this->assertEquals($expectedMax, $calculator->percentile($percentMax));

        $percentMin = PercentDataBuilder::aPercent()->withValue(0)->build();
        $expectedMin = PercentileDataBuilder::aPercentile()->withValue(0)->withPercent($percentMin)->build();

        $this->assertEquals($expectedMin, $calculator->percentile($percentMin));
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleValue()
    {
        $calculator = PercentileCalculatorDataBuilder::aPercentileCalculator()->withValues(25)->build();

        $this->assertEquals(25, $calculator->percentile(PercentDataBuilder::aPercent()->build())->value());
    }
}
