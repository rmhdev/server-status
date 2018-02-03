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
        if (!array_key_exists($id->value(), $this->measurements)) {
            return null;
        }

        return $this->measurements[$id->value()];
    }

    /**
     * @inheritdoc
     */
    public function add(Measurement $measurement): MeasurementRepository
    {
        $this->measurements[$measurement->id()->value()] = $measurement;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Measurement $measurement): MeasurementRepository
    {
        $id = $measurement->id()->value();
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
        $start = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $from->format(DATE_ISO8601));
        $start = $start->modify(sprintf("-%s seconds", $start->format("s")));

        $end = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $to->format(DATE_ISO8601));
        $end = $end->modify(sprintf("-%s seconds +1 minute", $end->format("s")));
        $rawData = [];
        $filtered = $this->filterByDateRange($check, $start, $end);
        foreach ($filtered as $measurement) {
            $groupBy = $measurement->dateCreated()->format("Y-m-d\TH:i");
            if (!array_key_exists($groupBy, $rawData)) {
                $rawData[$groupBy] = [
                    "date" => $measurement->dateCreated()->format("Y-m-d H:i:00"),
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
                "response_time" => $raw["count"] == 0 ? 0.00 : ($raw["sum"] / $raw["count"]),
            ];
        }

        return $data;
    }


    /**
     * @param Check $check
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
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
            if ($measurement->dateCreated() >= $to) {
                return false;
            }

            return true;
        });
    }

    private function measurements()
    {
        return $this->measurements;
    }
}
