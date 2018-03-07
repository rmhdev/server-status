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
        foreach ($this->dateRange()->dateRanges() as $dateRange) {
            $averages[] = $this->average($dateRange);
        }

        return $averages;
    }

    /**
     * @inheritdoc
     */
    public function average(DateRange $dateRange): SummaryAverage
    {
        return new SummaryAverage($dateRange, $this->filterValues($dateRange));
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
}
