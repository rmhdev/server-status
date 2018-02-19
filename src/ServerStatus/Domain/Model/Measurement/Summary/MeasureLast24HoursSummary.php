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

class MeasureLast24HoursSummary
{
    private $values;
    private $date;

    public function __construct($values = [], \DateTimeInterface $dateTime = null)
    {
        $this->values = $values;
        $date = $dateTime ? $dateTime : new \DateTimeImmutable("now");
        $this->date = $date->format(DATE_ISO8601);
    }

    protected function date(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(DATE_ISO8601, $this->date);
    }

    public function name(): string
    {
        return "last_24_hours";
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date()->modify("-24 hours");
    }

    public function to(): \DateTimeImmutable
    {
        return $this->date();
    }

    public function averages()
    {
        $averages = [];
        foreach ($this->dates() as $date) {
            $averages[] = $this->average($date);
        }

        return $averages;
    }

    private function average(\DateTimeInterface $date)
    {
        return [];
    }

    /**
     * @return \DateTimeImmutable[]
     */
    protected function dates(): array
    {
        $dates = [];
        $max = $this->to();
        $date = $this->from();
        while ($date < $max) {
            $dates[] = $date;
            $date = $date->modify(sprintf("+%d minutes", $this->groupedByMinutes()));
        }

        return $dates;
    }

    /**
     * @inheritdoc
     */
    public function groupedByMinutes(): int
    {
        return MeasureDaySummary::GROUPED_BY_MINUTES;
    }
}
