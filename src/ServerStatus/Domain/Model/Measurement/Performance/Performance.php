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

use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;

final class Performance
{
    const UPTIME_PERCENT_PRECISION = 4;
    const FIELD_MEAN = "mean";
    const FIELD_MEAN_95TH_PERCENTILE = "mean_95th";

    /**
     * @var int
     */
    private $totalMeasurements;

    /**
     * @var int
     */
    private $successfulMeasurements;

    private $responseMeanTimes;

    public function __construct(int $totalMeasurements, int $successfulMeasurements, $responseMeanTimes = [])
    {
        $this->totalMeasurements = $totalMeasurements;
        $this->successfulMeasurements = $successfulMeasurements;
        $this->responseMeanTimes = $this->processResponseMeanTimes($responseMeanTimes);
    }

    private function processResponseMeanTimes($responseMeanTimes = [])
    {
        $processed = [
            self::FIELD_MEAN => 0,
            self::FIELD_MEAN_95TH_PERCENTILE => 0
        ];
        foreach ($processed as $field => $value) {
            if (array_key_exists($field, $responseMeanTimes)) {
                $processed[$field] = $responseMeanTimes[$field];
            }
        }

        return $processed;
    }

    public function totalMeasurements(): int
    {
        return $this->totalMeasurements;
    }

    public function successfulMeasurements(): int
    {
        return $this->successfulMeasurements;
    }

    public function uptimePercent(): Percent
    {
        if (1 > $this->totalMeasurements()) {
            return Percent::createFromDecimalFraction(round(0, self::UPTIME_PERCENT_PRECISION));
        }

        return Percent::createFromDecimalFraction(
            $this->successfulMeasurements() / $this->totalMeasurements()
        );
    }

    /**
     * @return float Value in milliseconds
     */
    public function responseTimeMean(): float
    {
        return $this->getResponseMeanTime(self::FIELD_MEAN, 0);
    }

    /**
     * @return Percentile
     */
    public function responseTimePercentile(): Percentile
    {
        return new Percentile(
            Percent::createFromPercentage(95),
            $this->getResponseMeanTime(self::FIELD_MEAN_95TH_PERCENTILE, 0)
        );
    }

    private function getResponseMeanTime($name, $default = 0): float
    {
        if (!array_key_exists($name, $this->responseMeanTimes)) {
            return $default;
        }

        return $this->responseMeanTimes[$name];
    }
}
