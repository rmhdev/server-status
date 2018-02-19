<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Application\Service\Measurement;

use ServerStatus\Domain\Model\Check\Check;

class ViewDayMeasurementSummaryRequest
{
    private $check;
    private $date;

    public function __construct(Check $check, \DateTimeInterface $dateTime = null)
    {
        $date = $dateTime ? $dateTime : new \DateTime("now");
        $this->date = $date->format(DATE_ISO8601);
        $this->check = $check;
    }

    public function check(): Check
    {
        return $this->check;
    }

    public function date(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->date);
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date()->setTime(0, 0, 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->date()->setTime(23, 59, 59);
    }
}
