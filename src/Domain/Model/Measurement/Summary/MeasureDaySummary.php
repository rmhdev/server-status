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

namespace ServerStatus\Domain\Model\Measurement\Summary;

class MeasureDaySummary
{
    const GROUP_BY_MINUTES = 10;

    private $dateTime;

    public function __construct(\DateTimeInterface $dateTime = null)
    {
        $date = $dateTime ? $dateTime : new \DateTimeImmutable("now");

        $this->dateTime = $date->format(DATE_ISO8601);
    }

    protected function date(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(DATE_ISO8601, $this->dateTime);
    }

    public function name(): string
    {
        return "day";
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date()->setTime(0, 0, 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->date()->setTime(23, 59, 59);
    }

    /**
     * @return \DateTimeImmutable[]
     */
    public function dates(): array
    {
        $dates = [];
        $max = $this->to();
        $date = $this->from();
        while ($date <= $max) {
            $dates[] = $date;
            $date = $date->modify(sprintf("+%d minutes", $this->groupByMinutes()));
        }

        return $dates;
    }

    protected function groupByMinutes()
    {
        return self::GROUP_BY_MINUTES;
    }
}
