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

class MeasureDaySummary implements MeasureSummary
{
    const NAME = "day";
    const GROUPED_BY_MINUTES = 10;

    /**
     * @var array
     */
    private $values;

    /**
     * @var string
     */
    private $dateTime;

    public function __construct($values = [], \DateTimeInterface $dateTime = null)
    {
        $this->values = $values;
        $date = $dateTime ? $dateTime : new \DateTimeImmutable("now");
        $this->dateTime = $date->format(DATE_ISO8601);
    }

    protected function values(): array
    {
        return $this->values;
    }

    protected function date(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(DATE_ISO8601, $this->dateTime);
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return self::NAME;
    }

    /**
     * @inheritdoc
     */
    public function from(): \DateTimeImmutable
    {
        return $this->date()->setTime(0, 0, 0);
    }

    /**
     * @inheritdoc
     */
    public function to(): \DateTimeImmutable
    {
        return $this->date()->setTime(23, 59, 59);
    }

    /**
     * @inheritdoc
     */
    public function averages(): array
    {
        $averages = [];
        foreach ($this->dates() as $date) {
            $averages[] = $this->average($date);
        }

        return $averages;
    }

    /**
     * @inheritdoc
     */
    public function average(\DateTimeInterface $fromDate): SummaryAverage
    {
        list($start, $end) = $this->startEndDates($fromDate);

        return new SummaryAverage(
            $start,
            $end,
            $this->filterValues($start, $end)
        );
    }

    /**
     * Calculate the group start and end dates.
     *
     * @param \DateTimeInterface $fromDate
     * @return \DateTimeImmutable[]
     */
    protected function startEndDates(\DateTimeInterface $fromDate): array
    {
        $date = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $fromDate->format(DATE_ISO8601));
        $formattedMinute = (int) $date->format("i");
        $formattedMinute -= $formattedMinute % self::GROUPED_BY_MINUTES;
        $start = $date->setTime((int) $date->format("H"), $formattedMinute, 0);
        $end = $start->modify(sprintf("+%d minutes - 1 second", self::GROUPED_BY_MINUTES));

        return [$start, $end];
    }

    protected function filterValues(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return array_filter($this->values(), function ($value) use ($from, $to) {
            if (!array_key_exists("date", $value)) {
                return false;
            }
            $date = new \DateTimeImmutable($value["date"]);

            return $date >= $from && $date <= $to;
        });
    }

    /**
     * @return \DateTimeImmutable[]
     */
    protected function dates(): array
    {
        $dates = [];
        $max = $this->to();
        $date = $this->from();
        while ($date <= $max) {
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
        return self::GROUPED_BY_MINUTES;
    }
}
