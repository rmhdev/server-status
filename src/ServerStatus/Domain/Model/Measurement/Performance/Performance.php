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

use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;

final class Performance
{
    /**
     * @var PerformanceStatusCollection
     */
    private $performanceStatusCollection;

    /**
     * @var Percentile
     */
    private $percentile;

    /**
     * Internal variable
     *
     * @var int
     */
    private $totalMeasurements;

    /**
     * Internal variable
     *
     * @var int
     */
    private $successfulMeasurements;


    public function __construct(PerformanceStatusCollection $collection, Percentile $percentile)
    {
        $this->performanceStatusCollection = $collection;
        $this->percentile = $percentile;
        $this->totalMeasurements = null;
        $this->successfulMeasurements = null;
    }

    public function performanceStatusCollection(): PerformanceStatusCollection
    {
        return $this->performanceStatusCollection;
    }

    public function percentile(): Percentile
    {
        return $this->percentile;
    }

    public function totalMeasurements(): int
    {
        if (is_null($this->totalMeasurements)) {
            $this->totalMeasurements = $this->performanceStatusCollection()->count();
        }
        return $this->totalMeasurements;
    }

    public function correctMeasurements(): int
    {
        if (is_null($this->successfulMeasurements)) {
            $this->successfulMeasurements = $this->performanceStatusCollection()->filterByClassResponse(
                MeasurementStatus::correctClassResponses()
            )->count();
        }
        return $this->successfulMeasurements;
    }

    public function uptimePercent(): Percent
    {
        if (1 > $this->totalMeasurements()) {
            return Percent::createFromDecimalFraction(0.00);
        }

        return Percent::createFromDecimalFraction(
            $this->correctMeasurements() / $this->totalMeasurements()
        );
    }

    /**
     * @return MeasurementDuration
     */
    public function responseTimeMean(): MeasurementDuration
    {
        return $this->performanceStatusCollection()->averageDuration();
    }

    /**
     * @return Percentile
     * @deprecated
     */
    public function responseTimePercentile(): Percentile
    {
        return $this->percentile();
    }
}
