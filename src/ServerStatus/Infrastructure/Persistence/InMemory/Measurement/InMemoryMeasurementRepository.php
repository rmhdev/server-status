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
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementDoesNotExistException;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

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
    public function add(Measurement $measurement): MeasurementRepository
    {
        $this->measurements[$measurement->id()->id()] = $measurement;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Measurement $measurement): MeasurementRepository
    {
        $id = $measurement->id()->id();
        if (!array_key_exists($id, $this->measurements)) {
            throw new MeasurementDoesNotExistException(
                sprintf('Measurement "%s" is not in the repository', $id)
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
    public function summaryByMinute(Check $check, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return $this->createSummaryBy($check, $from, $to, "Y-m-d H:i:00");
    }

    private function createSummaryBy(
        Check $check,
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        $groupByDateFormat = "Y-m-d H:00:00"
    ): array {
        $rawData = [];
        foreach ($this->filterByDateRange($check, $start, $end) as $measurement) {
            $groupBy = $measurement->dateCreated()->format($groupByDateFormat);
            if (!array_key_exists($groupBy, $rawData)) {
                $rawData[$groupBy] = [
                    "date" => $measurement->dateCreated()->format($groupByDateFormat),
                    "count" => 0,
                    "sum" => 0
                ];
            }
            $rawData[$groupBy]["count"] += 1;
            $rawData[$groupBy]["sum"] += 0;
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
     * @return Measurement[]
     */
    private function filterByDateRange(Check $check, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return array_filter($this->measurements(), function (Measurement $measurement) use ($check, $from, $to) {
            if (!$measurement->check()->id()->equals($check->id())) {
                return false;
            }
            if ($measurement->dateCreated() < $from) {
                return false;
            }
            if ($measurement->dateCreated() > $to) {
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

    public function summaryByHour(Check $check, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return $this->createSummaryBy($check, $from, $to, "Y-m-d H:00:00");
    }
}
