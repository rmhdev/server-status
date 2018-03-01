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
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReport;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReportFactory;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementResultDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementStatusDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentDataBuilder;
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
                PercentileDataBuilder::aPercentile()->withValue(0)->withPercent(
                    PercentDataBuilder::aPercent()->withValue(0.95)->build()
                )->build()
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
    public function isShouldReturnReportWithDayData()
    {
        $date = new \DateTimeImmutable("2018-01-28T23:00:00+0200");
        $check = CheckDataBuilder::aCheck()->build();
        $dateRange = DateRangeFactory::create(DateRangeDay::NAME, $date);
        $factory = $this->createPerformanceReportFactory($check, $date);
        $performanceReport = $factory->create($check, $dateRange);

        $this->assertEquals(12, $performanceReport->performance()->totalMeasurements());
    }

    private function createPerformanceReportFactory(Check $check, \DateTimeImmutable $date): PerformanceReportFactory
    {
        $repo = new InMemoryMeasurementRepository();
        foreach ($this->measurementsForCheckAndDay($check, $date) as $measurement) {
            $repo->add($measurement);
        }
        return new PerformanceReportFactory($repo);
    }

    /**
     * @param Check $check
     * @param \DateTimeImmutable $date
     * @return Measurement[]
     */
    private function measurementsForCheckAndDay(Check $check, \DateTimeImmutable $date): array
    {
        $statusCodes = [
            200, 200, 301, 200, 301, 404, 200, 200, 500, 200, 200, 200,
            200, 200, 301, 200, 301, 404, 200, 200, 500, 200, 200
        ];

        $measurements = [];
        foreach ($statusCodes as $i => $statusCode) {
            $measurements[] = MeasurementDataBuilder::aMeasurement()
                ->withCheck($check)
                ->withDate($date->setTime($i, 0, 0))
                ->withResult(
                    MeasurementResultDataBuilder::aMeasurementResult()
                        ->withStatus(MeasurementStatusDataBuilder::aMeasurementStatus()->withCode($statusCode)->build())
                        ->build()
                )->build();
        }

        return $measurements;
    }
}
