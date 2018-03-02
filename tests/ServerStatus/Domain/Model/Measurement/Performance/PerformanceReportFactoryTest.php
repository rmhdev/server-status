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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReport;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReportFactory;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentileDataBuilder;

class PerformanceReportFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnAnEmptyReportWhenNoDataIsFound()
    {
        $check = CheckDataBuilder::aCheck()->build();
        $dateRange = DateRangeFactory::create(
            DateRangeDay::NAME,
            new \DateTimeImmutable("2018-01-28T23:00:00+0200")
        );
        $expected = new PerformanceReport($check, $dateRange, $this->createPerformance([]));
        $factory = $this->createEmptyPerformanceReportFactory();

        $this->assertEquals($expected, $factory->create($check, $dateRange));
    }

    private function createPerformance($performanceStatuses = [])
    {
        return PerformanceDataBuilder::aPerformance()
            ->withPerformanceStatusCollection(
                PerformanceStatusCollectionDataBuilder::aPerformanceStatusCollection()
                    ->withValues($performanceStatuses)->build()
            )
            ->withPercentile(
                PercentileDataBuilder::aPercentile()->withValue(0)->withPercent(0.95)->build()
            )
            ->build();
    }

    private function createEmptyPerformanceReportFactory()
    {
        return new PerformanceReportFactory(
            new InMemoryMeasurementRepository()
        );
    }

    /**
     * @test
     */
    public function isShouldReturnDefaultPercentile()
    {
        $check = CheckDataBuilder::aCheck()->build();
        $dateRange = DateRangeFactory::create(
            DateRangeDay::NAME,
            new \DateTimeImmutable("2018-01-28T23:00:00+0200")
        );
        $factory = $this->createEmptyPerformanceReportFactory();

        $this->assertEquals(
            0.95,
            $factory->create($check, $dateRange)->performance()->percentile()->percent()->decimal(),
            'It should return the default percentile percent'
        );
    }
}
