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

namespace ServerStatus\Tests\Domain\Model\Measurement\Performance;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Measurement\Performance\Performance;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentileDataBuilder;

class PerformanceTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCalculateUptimePercent()
    {
        $performance = PerformanceDataBuilder::aPerformance()
            ->withTotalMeasurements(100)
            ->withSuccessfulMeasurements(99)
            ->build();

        $this->assertEquals(0.99, $performance->uptimePercent());
    }

    /**
     * @test
     */
    public function itShouldReturnZeroWhenNoDataIsDefined()
    {
        $performance = PerformanceDataBuilder::aPerformance()->build();
        $expectedPercentile = PercentileDataBuilder::aPercentile()->withValue(0)->withPercent(
            PercentDataBuilder::aPercent()->withValue(0.95)->build()
        )->build();

        $this->assertSame(0.00, $performance->responseTimeMean());
        $this->assertEquals($expectedPercentile, $performance->responseTime95th());
    }

    /**
     * @test
     */
    public function itShouldReturnDefinedMeanValues()
    {
        $performance = PerformanceDataBuilder::aPerformance()->withMeans([
            Performance::FIELD_MEAN => 123.1234,
            Performance::FIELD_MEAN_95TH_PERCENTILE => 567.5678,
        ])->build();
        $expectedPercentile = PercentileDataBuilder::aPercentile()->withValue(567.5678)->withPercent(
            PercentDataBuilder::aPercent()->withValue(0.95)->build()
        )->build();

        $this->assertSame(123.1234, $performance->responseTimeMean());
        $this->assertEquals($expectedPercentile, $performance->responseTime95th());
    }
}
