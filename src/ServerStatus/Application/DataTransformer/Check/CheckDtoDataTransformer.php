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

namespace ServerStatus\Application\DataTransformer\Check;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;
use ServerStatus\Domain\Model\Measurement\Summary\SummaryAverage;

final class CheckDtoDataTransformer implements CheckDataTransformer
{
    /**
     * @var Check
     */
    private $check;

    /**
     * @var MeasureSummary
     */
    private $measureSummary;


    public function write(Check $check, MeasureSummary $measureSummary)
    {
        $this->check = $check;
        $this->measureSummary = $measureSummary;
    }

    public function read()
    {
        return [
            "customer" => $this->processCustomer(),
            "check" => $this->processCheck(),
            "measureSummary" => $this->processMeasureSummary()
        ];
    }

    private function processCustomer()
    {
        return [
            "id" => $this->check->customer()->id()->id(),
            "name" => $this->check->customer()->screenName(),
        ];
    }

    private function processCheck()
    {
        return [
            "id" => $this->check->id()->id(),
            "name" => $this->check->name()->value(),
            "slug" => $this->check->name()->slug(),
        ];
    }

    private function processMeasureSummary()
    {
        return [
            "date_range" => [
                "from" => $this->measureSummary->dateRange()->from()->format(DATE_ISO8601),
                "to" => $this->measureSummary->dateRange()->to()->format(DATE_ISO8601),
                "name" => $this->measureSummary->dateRange()->name(),
                "formatted" => $this->measureSummary->dateRange()->formatted(),
            ],
            "averages" => $this->processMeasureSummaryAverages(),
        ];
    }

    private function processMeasureSummaryAverages()
    {
        /* @var $previousSummaryAverage SummaryAverage|null */
        $previousSummaryAverage = null;
        $values = [];
        foreach ($this->measureSummary->averages() as $average) {
            $diff = new Percent(0);
            if ($previousSummaryAverage) {
                $diff = $average->responseTime()->diff($previousSummaryAverage->responseTime());
            }
            $values[] = [
                "response_time" => [
                    "value" => $average->responseTime()->value(),
                    "formatted" => $average->responseTime()->formatted(),
                    "diff" => [
                        "value" => $diff->decimal(),
                        "formatted" => (string) $diff
                    ]
                ]
            ];
            $previousSummaryAverage = $average;
        }

        return $values;
    }
}
