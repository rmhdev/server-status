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
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDurationDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementStatusDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentDataBuilder;

class PerformanceTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCalculateUptimePercent()
    {
        $performanceStatuses = [];
        for ($i = 0; $i < 99; $i += 1) {
            $performanceStatuses[] = PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(200)->build()
            )->build();
        }
        $performanceStatuses[] = PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
            MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(404)->build()
        )->build();

        $performance = PerformanceDataBuilder::aPerformance()
            ->withPerformanceStatusCollection(
                PerformanceStatusCollectionDataBuilder::aPerformanceStatusCollection()
                    ->withValues($performanceStatuses)->build()
            )
            ->build();

        $this->assertEquals(
            PercentDataBuilder::aPercent()->withValue(0.99)->build(),
            $performance->uptimePercent()
        );
    }

    /**
     * @test
     */
    public function itShouldReturnZeroWhenNoDataIsDefined()
    {
        $this->assertEquals(
            MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(0)->build(),
            PerformanceDataBuilder::aPerformance()->build()->responseTimeMean()
        );
    }

    /**
     * @test
     */
    public function itShouldSumTheNumberOfMeasurements()
    {
        $performanceStatuses = [
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(200)->build()
            )->withCount(10)->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(300)->build()
            )->withCount(5)->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(404)->build()
            )->withCount(2)->build(),
        ];
        $collection = PerformanceStatusCollectionDataBuilder::aPerformanceStatusCollection()
            ->withValues($performanceStatuses)
            ->build();
        $performance = PerformanceDataBuilder::aPerformance()
            ->withPerformanceStatusCollection($collection)
            ->build();

        $this->assertEquals(17, $performance->totalMeasurements());
        $this->assertEquals(15, $performance->correctMeasurements());
    }
}
