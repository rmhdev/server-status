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

namespace ServerStatus\Infrastructure\Persistence\InMemory\Measurement;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementDoesNotExistException;
use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatus;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatusCollection;
use ServerStatus\ServerStatus\Domain\Model\Measurement\Percentile\PercentileCalculator;

class InMemoryMeasurementRepository implements MeasurementRepository
{
    private $measurements;

    public function __construct()
    {
        $this->measurements = [];
    }

    /**
     * @inheritdoc
     */
    public function ofId(MeasurementId $id): ?Measurement
    {
        if (!array_key_exists($id->id(), $this->measurements)) {
            return null;
        }

        return $this->measurements[$id->id()];
    }

    /**
     * @inheritdoc
     */
    public function add($measurement): MeasurementRepository
    {
        if (!is_iterable($measurement)) {
            $measurement = [$measurement];
        }
        foreach ($measurement as $item) {
            $this->assertAddMeasurement($item);
            $this->measurements[$item->id()->id()] = $item;
        }

        return $this;
    }

    private function assertAddMeasurement($measurement)
    {
        if (!is_object($measurement)) {
            throw new \UnexpectedValueException(sprintf(
                'Only Measurement objects can be added to repository, "%s" received',
                gettype($measurement)
            ));
        }
        if (!$measurement instanceof Measurement) {
            throw new \UnexpectedValueException(sprintf(
                'Only Measurement objects can be added to repository, "%s" received',
                get_class($measurement)
            ));
        }
    }

    /**
     * @inheritdoc
     */
    public function remove(Measurement $measurement): MeasurementRepository
    {
        $id = $measurement->id()->id();
        if (!array_key_exists($id, $this->measurements)) {
            throw new MeasurementDoesNotExistException(
                sprintf('Measurement "%s" cannto be removed from "in memory" repository', $id)
            );
        }
        unset($this->measurements[$id]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): MeasurementId
    {
        return new MeasurementId();
    }

    /**
     * @inheritdoc
     */
    public function summaryValues(Check $check, DateRange $dateRange)
    {
        return $this->createSummaryBy($check, $dateRange, "Y-m-d H:i:00");
    }

    private function createSummaryBy(
        Check $check,
        DateRange $dateRange,
        $groupByDateFormat = "Y-m-d H:00:00"
    ): array {
        $rawData = [];

        foreach ($this->filterByDateRange($check, $dateRange) as $measurement) {
            $groupBy = $this->createBaseDateFromInterval(
                $dateRange,
                $measurement->dateCreated()
            )->format("Y-m-d H:i:s");

            if (!array_key_exists($groupBy, $rawData)) {
                $rawData[$groupBy] = [
                    "date" => $measurement->dateCreated()->format($groupByDateFormat),
                    "count" => 0,
                    "sum" => 0
                ];
            }
            $rawData[$groupBy]["count"] += 1;
            $rawData[$groupBy]["sum"] += $measurement->result()->duration()->value();
        }

        $data = [];
        foreach ($rawData as $raw) {
            $data[] = [
                "date" => $raw["date"],
                "count" => $raw["count"],
                "response_time" => $raw["count"] == 0 ? 0.00 : ($raw["sum"] / $raw["count"]),
            ];
        }
        return $data;
    }

    /**
     * TODO: This method should be optimized.
     *
     * @param DateRange $dateRange
     * @param \DateTimeInterface $date
     * @return \DateTimeImmutable
     */
    private function createBaseDateFromInterval(DateRange $dateRange, \DateTimeInterface $date): \DateTimeImmutable
    {
        foreach ($dateRange->dateRanges() as $range) {
            if ($range->isInBounds($date)) {
                return $range->from();
            }
        }

        throw new \UnexpectedValueException("Cannot convert from date interval");
    }

    /**
     * @return Measurement[]
     */
    private function filterByDateRange(Check $check, DateRange $dateRange)
    {
        return array_filter($this->measurements(), function (Measurement $measurement) use ($check, $dateRange) {
            if (!$measurement->check()->id()->equals($check->id())) {
                return false;
            }
            if (!$dateRange->isInBounds($measurement->dateCreated())) {
                return false;
            }

            return true;
        });
    }

    /**
     * @return Measurement[]
     */
    private function measurements()
    {
        return $this->measurements;
    }

    public function countAll(): int
    {
        return sizeof($this->measurements);
    }

    public function findPercentile(Check $check, DateRange $dateRange, Percent $percent): Percentile
    {
        $times = [];
        $byStatus = [];
        foreach ($this->filterByDateRange($check, $dateRange) as $measurement) {
            $times[] = $measurement->result()->duration()->value();
            $statusCode = $measurement->result()->status()->code();
            if (!array_key_exists($statusCode, $byStatus)) {
                $byStatus[$statusCode] = [
                    "status" => $measurement->result()->status(),
                    "duration" => 0,
                    "count" => 0
                ];
            }
            $byStatus[$statusCode]["duration"] += $measurement->result()->duration()->value();
            $byStatus[$statusCode]["count"] += 1;
        }
        $percentileCalculator = new PercentileCalculator($times);

        return $percentileCalculator->percentile($percent);
    }

    public function calculatePerformanceStatus(Check $check, DateRange $dateRange): PerformanceStatusCollection
    {
        $values = [];
        foreach ($this->filterByDateRange($check, $dateRange) as $measurement) {
            $code = $measurement->result()->status()->code();
            if (!array_key_exists($code, $values)) {
                $values[$code] = [
                    "status" => $code,
                    "count" => 0,
                    "duration" => 0,
                ];
            }
            $values[$code]["count"] += 1;
            $values[$code]["duration"] += $measurement->result()->duration()->value();
        }
        $performanceStatuses = [];
        foreach ($values as $code => $value) {
            $avgDuration = $values[$code]["duration"] / $values[$code]["count"];
            $performanceStatuses[] = new PerformanceStatus(
                new MeasurementStatus($code),
                new MeasurementDuration($avgDuration),
                $values[$code]["count"]
            );
        }

        return new PerformanceStatusCollection($performanceStatuses);
    }
}
