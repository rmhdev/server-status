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

namespace ServerStatus\Model\Measurement;

class Measurement
{
    private $id;
    private $dateCreated;

    public function __construct(MeasurementId $id, \DateTimeInterface $dateCreated)
    {
        $this->id = $id;
        $this->dateCreated = $this->createDateTime($dateCreated);
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
}
