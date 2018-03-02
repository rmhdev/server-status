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

final class PerformanceStatusCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var PerformanceStatus[]
     */
    private $performanceStatuses;


    public function __construct($performanceStatuses = [])
    {
        $this->performanceStatuses = $this->processPerformanceStatuses($performanceStatuses);
    }

    private function processPerformanceStatuses($performanceStatuses = []): array
    {
        if (!is_iterable($performanceStatuses)) {
            $performanceStatuses = [$performanceStatuses];
        }
        $processed = [];
        foreach ($performanceStatuses as $performanceStatus) {
            $this->assertPerformanceStatus($performanceStatus);
            $processed[] = $performanceStatus;
        }

        return $processed;
    }

    private function assertPerformanceStatus($performanceStatus)
    {
        if (!is_object($performanceStatus) || !$performanceStatus instanceof PerformanceStatus) {
            throw new \UnexpectedValueException(sprintf(
                'PerformanceStatusCollection only accepts "PerformanceStatus" objects, "%s" received',
                gettype($performanceStatus)
            ));
        }
    }

    public function count(): int
    {
        return sizeof($this->performanceStatuses);
    }

    /**
     * @return \ArrayIterator|PerformanceStatus[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->performanceStatuses);
    }

    public function averageDuration(MeasurementStatus $status = null): MeasurementDuration
    {
        $durations = array_map(
            function (PerformanceStatus $status) {
                return $status->durationAverage()->value();
            },
            $this->filterByStatus($status)->getIterator()->getArrayCopy()
        );
        if (!sizeof($durations)) {
            return new MeasurementDuration(0);
        }

        return new MeasurementDuration(array_sum($durations) / sizeof($durations));
    }

    public function filterByStatus(MeasurementStatus $status = null): PerformanceStatusCollection
    {
        if (is_null($status)) {
            return new PerformanceStatusCollection($this->performanceStatuses);
        }
        $filtered = [];
        foreach ($this->performanceStatuses as $performanceStatus) {
            if ($performanceStatus->status()->equals($status)) {
                $filtered[] = $performanceStatus;
            }
        }

        return new PerformanceStatusCollection($filtered);
    }

    /**
     * @param int|int[] $classResponses
     * @return \ArrayIterator
     */
    public function filterByClassResponse($classResponses): \ArrayIterator
    {
        if (!is_array($classResponses)) {
            $classResponses = [$classResponses];
        }
        $filtered = [];
        foreach ($this->performanceStatuses as $performanceStatus) {
            if (in_array($performanceStatus->status()->classResponse(), $classResponses)) {
                $filtered[] = $performanceStatus;
            }
        }

        return new \ArrayIterator($filtered);
    }
}
