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

namespace ServerStatus\Tests\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class MeasurementDataBuilder
{
    private $id;
    private $date;
    private $check;

    public function __construct()
    {
        $this->id = MeasurementIdDataBuilder::aMeasurementId()->build();
        $this->date = new \DateTimeImmutable("2018-01-28 23:00:00", new \DateTimeZone("Europe/Madrid"));
        $this->check = CheckDataBuilder::aCheck()->build();
    }

    public function withId(MeasurementId $id): MeasurementDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function withDate(\DateTimeInterface $dateTime): MeasurementDataBuilder
    {
        $this->date = \DateTimeImmutable::createFromFormat(
            DATE_ISO8601,
            $dateTime->format(DATE_ISO8601),
            $dateTime->getTimezone()
        );

        return $this;
    }

    public function withCheck(Check $check): MeasurementDataBuilder
    {
        $this->check = $check;

        return $this;
    }

    public function build(): Measurement
    {
        $date = \DateTime::createFromFormat(
            DATE_ISO8601,
            $this->date->format(DATE_ISO8601),
            $this->date->getTimezone()
        );

        return new Measurement($this->id, $date, $this->check);
    }

    public static function aMeasurement(): MeasurementDataBuilder
    {
        return new self();
    }
}
