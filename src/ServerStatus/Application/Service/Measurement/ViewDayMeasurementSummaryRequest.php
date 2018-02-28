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
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;

class ViewDayMeasurementSummaryRequest
{
    /**
     * @var Check
     */
    private $check;

    /**
     * @var DateRange
     */
    private $dateRange;


    public function __construct(Check $check, DateRange $dateRange = null)
    {
        $this->check = $check;
        $this->dateRange = $dateRange ? $dateRange : $this->defaultDateRange();
    }

    private function defaultDateRange(): DateRange
    {
        return DateRangeFactory::create(DateRangeLast24Hours::NAME, new \DateTimeImmutable("now"));
    }

    public function check(): Check
    {
        return $this->check;
    }

    public function dateRange(): DateRange
    {
        return $this->dateRange;
    }
}
