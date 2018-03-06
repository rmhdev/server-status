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

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;

class MeasureSummary
{
    const NAME = "day";
    const GROUPED_BY_MINUTES = 10;

    /**
     * @var Check
     */
    private $check;

    /**
     * @var array
     */
    private $values;

    /**
     * @var DateRange
     */
    private $dateRange;


    public function __construct(Check $check, $values = [], DateRange $dateRange = null)
    {
        $this->check = $check;
        $this->values = $values;
        $this->dateRange = $dateRange;
    }

    protected function values(): array
    {
        return $this->values;
    }

    public function check(): Check
    {
        return $this->check;
    }

    public function dateRange(): DateRange
    {
        return $this->dateRange;
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return self::NAME;
    }

    /**
     * @return SummaryAverage[]
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
        $dateRange = $this->createDateRange($fromDate);

        return new SummaryAverage($dateRange, $this->filterValues($dateRange));
    }

    /**
     * Calculate the group start and end dates.
     *
     * @param \DateTimeInterface $fromDate
     * @return DateRange
     */
    protected function createDateRange(\DateTimeInterface $fromDate): DateRange
    {
        $date = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $fromDate->format(DATE_ISO8601));
        $formattedMinute = (int) $date->format("i");
        $formattedMinute -= $formattedMinute % self::GROUPED_BY_MINUTES;
        $start = $date->setTime((int) $date->format("H"), $formattedMinute, 0);
        $end = $start->modify(sprintf("+%d minutes", self::GROUPED_BY_MINUTES));

        return DateRangeFactory::createCustom($start, $end);
    }

    protected function filterValues(DateRange $dateRange): array
    {
        return array_filter($this->values(), function ($value) use ($dateRange) {
            if (!array_key_exists("date", $value)) {
                return false;
            }
            $date = new \DateTimeImmutable($value["date"]);

            return $dateRange->isInBounds($date);
        });
    }

    /**
     * @return \DateTimeImmutable[]
     */
    protected function dates(): array
    {
        $dates = [];
        $max = $this->dateRange()->to();
        $date = $this->dateRange()->from();
        while ($date < $max) {
            $dates[] = $date;
            //$date = $date->modify(sprintf("+%d minutes", $this->groupedByMinutes()));
            $date = $date->add($this->dateRange->interval());
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
