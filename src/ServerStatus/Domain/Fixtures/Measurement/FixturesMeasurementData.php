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
use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementFactory;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementResult;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;

final class FixturesMeasurementData
{
    const MINUTES_BETWEEN_MEASUREMENTS_CONDENSED = 10;
    const MINUTES_BETWEEN_MEASUREMENTS = 60;
    const MINUTES_PER_DAY = 1440;
    const AVAILABLE_MONTHS = 1;
    const START_DATE = "2018-01-01T00:00:00+0200";
    const NUM_WEEK_IN_YEAR_CONDENSED = 1;

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
        /* @var \DateTimeImmutable $start */
        $weekNumber = $dateTime->format("W");
        $start = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $dateTime->format(DATE_ISO8601))
            ->setTime(0, 0, 0)
            ->getTimestamp();
        $measurements = [];

        /**
         * To minimize db size:
         * We will add more measurements only for a given week of the year;
         * the rest of dates will have less measurements.
         */
        $addMinutes = self::NUM_WEEK_IN_YEAR_CONDENSED == $weekNumber ?
            self::MINUTES_BETWEEN_MEASUREMENTS_CONDENSED :
            self::MINUTES_BETWEEN_MEASUREMENTS;

        for ($i = 0; $i < self::MINUTES_PER_DAY; $i += $addMinutes) {
            $dateCreated = \DateTime::createFromFormat(DATE_ISO8601, date(DATE_ISO8601, $start + ($i * 60)));
            $measurements[] = $this->factory->build(
                new MeasurementId($check->id()->id() . "-" . $dateCreated->format("Y-m-d") . "-" . $i),
                $dateCreated,
                $check,
                $this->createMeasurementResult($dateCreated)
            );
        }

        return $measurements;
    }

    private function createMeasurementResult(\DateTimeInterface $dateTime): MeasurementResult
    {
        $duration = 200;
        $memory = 8000;
        if (6 > $dateTime->format("N")) {
            // date is NOT weekend
            $duration += 75 * ((int) $dateTime->format("N"));
            $memory += 4000 * ((int) $dateTime->format("N"));
        } elseif (6 == $dateTime->format("N")) {
            // date is saturday
            $duration += 75;
            $memory += 4000;
        }
        $time = ((int) $dateTime->format("H")) * 60 + ((int) $dateTime->format("i"));
        $dayOscillation = sin(M_PI / 2 + 2 * M_PI * $time / (24 * 60));
        $duration += 23 * $dayOscillation;

        $code = 200;
        $reasonPhrase = "test ok";
        if (5 == $dateTime->format("N") && ("11:00" == $dateTime->format("H:i"))) {
            $code = 500;
            $reasonPhrase = "server unavailable";
        }
        if (1 == $dateTime->format("N") && ("08:00" == $dateTime->format("H:i"))) {
            $code = 404;
            $reasonPhrase = "page not found";
        }
        if (3 == $dateTime->format("N") && ("17:00" == $dateTime->format("H:i"))) {
            $code = 0;
            $reasonPhrase = "something happened!";
        }

        return new MeasurementResult(
            new MeasurementStatus($code, $reasonPhrase),
            new MeasurementDuration(max(50, $duration)),
            $memory
        );
    }
}
