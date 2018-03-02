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

namespace ServerStatus\Domain\Model\Measurement\Performance;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;

final class PerformanceReportFactory
{
    const DEFAULT_PERCENTILE = 0.95;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;


    public function __construct(MeasurementRepository $repository)
    {
        $this->measurementRepository = $repository;
    }

    public function create(Check $check, DateRange $dateRange): PerformanceReport
    {
        return new PerformanceReport(
            $check,
            $dateRange,
            $this->createPerformance($check, $dateRange)
        );
    }

    private function createPerformance(Check $check, DateRange $dateRange)
    {
        return new Performance(
            $this->measurementRepository->calculatePerformanceStatus($check, $dateRange),
            $this->measurementRepository->findPercentile($check, $dateRange, self::defaultPercent())
        );
    }

    public static function defaultPercent(): Percent
    {
        return new Percent(self::DEFAULT_PERCENTILE);
    }
}
