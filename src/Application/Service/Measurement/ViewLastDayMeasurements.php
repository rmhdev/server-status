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

class ViewLastDayMeasurements
{
    private $date;

    public function __construct(\DateTimeInterface $dateTime = null)
    {
        $date = $dateTime ? $dateTime : new \DateTime("now");
        $this->date = $date->format(DATE_ISO8601);
    }

    public function date(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->date);
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date()->modify("-24 hours");
    }
}
