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

namespace ServerStatus\Domain\Fixtures\Measurement;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementFactory;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementResult;

final class FixturesMeasurementData
{
    const MINUTES_BETWEEN_MEASUREMENTS = 5;
    const MINUTES_PER_DAY = 1440;
    const AVAILABLE_MONTHS = 1;
    const START_DATE = "2018-01-01T00:00:00+0200";

    /**
     * @var MeasurementRepository
     */
    private $repository;

    /**
     * @var MeasurementFactory
     */
    private $factory;

    /**
     * @var CheckRepository
     */
    private $checkRepository;


    public function __construct(
        MeasurementRepository $repository,
        MeasurementFactory $factory,
        CheckRepository $checkRepository
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->checkRepository = $checkRepository;
    }

    public function load()
    {
        $this->repository->add($this->values());
    }

    public function values()
    {
        $end = (new \DateTimeImmutable(self::START_DATE))->modify(sprintf("+%d months", self::AVAILABLE_MONTHS));
        $checkIds = [
            "rober-check-1",
            "rober-check-2",
            "laura-check-1",
        ];
        $values = [];
        foreach ($checkIds as $checkId) {
            $check = $this->checkRepository->ofId(new CheckId($checkId));
            $date = new \DateTime(self::START_DATE);
            while ($date < $end) {
                $values = array_merge($values, $this->createMeasurementsForDate($check, $date));
                $date = $date->modify("+1 day");
            }
        }

        return $values;
    }

    /**
     * @param Check $check
     * @param \DateTimeInterface $dateTime
     * @return Measurement[]
     */
    private function createMeasurementsForDate(Check $check, \DateTimeInterface $dateTime): array
    {
        $start = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $dateTime->format(DATE_ISO8601))
            ->setTime(0, 0, 0)
            ->getTimestamp();
        $measurements = [];
        for ($i = 0; $i < self::MINUTES_PER_DAY; $i += self::MINUTES_BETWEEN_MEASUREMENTS) {
            $measurements[] = $this->factory->build(
                new MeasurementId($check->id()->id() . "-" . $dateTime->format("Y-m-d") . "-" . $i),
                \DateTime::createFromFormat(DATE_ISO8601, date(DATE_ISO8601, $start + ($i * 60))),
                $check,
                $this->createMeasurementResult()
            );
        }

        return $measurements;
    }

    private function createMeasurementResult(): MeasurementResult
    {
        return new MeasurementResult(
            200,
            "test ok",
            1000,
            32000
        );
    }
}
