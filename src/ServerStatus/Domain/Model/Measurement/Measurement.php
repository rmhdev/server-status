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

namespace ServerStatus\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Check\Check;

class Measurement
{
    private $id;
    private $dateCreated;
    private $check;
    private $result;

    public function __construct(
        MeasurementId $id,
        \DateTimeInterface $dateCreated,
        Check $check,
        MeasurementResult $result
    ) {
        $this->id = $id;
        $this->dateCreated = $this->createDateTime($dateCreated);
        $this->check = $check;
        $this->result = $result;
    }

    private function createDateTime(\DateTimeInterface $dateTime)
    {
        return \DateTime::createFromFormat(
            DATE_ISO8601,
            $dateTime->format(DATE_ISO8601),
            $dateTime->getTimezone()
        );
    }

    public function id(): MeasurementId
    {
        return $this->id;
    }

    public function dateCreated(): \DateTime
    {
        return clone $this->dateCreated;
    }

    public function check(): Check
    {
        return $this->check;
    }

    public function result(): MeasurementResult
    {
        return $this->result;
    }

    /**
     * @return int &lt; 0 if $this is less than
     * $measurement; &gt; 0 if $this
     * is greater than $measurement, and 0 if they are
     * equal.
     */
    public function compareTo(Measurement $measurement): int
    {
        if ($measurement->dateCreated() > $this->dateCreated()) {
            return -1;
        }
        if ($measurement->dateCreated() < $this->dateCreated()) {
            return 1;
        }

        return 0;
    }
}
