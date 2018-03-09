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

namespace ServerStatus\Application\DataTransformer\Measurement;

use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReport;

final class PerformanceReportDtoDataTransformer implements PerformanceReportDataTransformer
{
    /**
     * @var PerformanceReport
     */
    private $report;

    public function write(PerformanceReport $report)
    {
        $this->report = $report;
    }

    public function read()
    {
        return [
            "check" => $this->readCheck(),
            "date_range" => $this->readDateRange(),
            "performance" => $this->readPerformance(),
        ];
    }

    private function readCheck()
    {
        return [
            "id" => $this->report->check()->id()->id(),
            "name" => $this->report->check()->name(),
            "url" => [
                "formatted" => (string) $this->report->check()->url(),
            ],
            "status" => [
                "name" => $this->report->check()->status()->name(),
                "is_enabled" => $this->report->check()->status()->isEnabled() ? '1' : '0',
            ]
        ];
    }

    private function readDateRange()
    {
        return [
            "name" => $this->report->dateRange()->name(),
            "from" => $this->report->dateRange()->from()->format(DATE_ISO8601),
            "to" => $this->report->dateRange()->to()->format(DATE_ISO8601),
            "formatted" => $this->report->dateRange()->formatted(),
        ];
    }

    private function readPerformance()
    {
        $status = [];
        foreach ($this->report->performance()->performanceStatusCollection() as $status) {
            $status[] = [
                "code" => $status->status()->code(),
                "count" => $status->count(),
                "average" => [
                    "value" => $status->durationAverage()->value(),
                    "formatted" => $status->durationAverage()->formatted(),
                ]
            ];
        }
        $percentileDuration = new MeasurementDuration($this->report->performance()->percentile()->value());

        return [
            "measurements" => [
                "total" => $this->report->performance()->totalMeasurements(),
                "correct" => $this->report->performance()->correctMeasurements(),
            ],
            "uptime" => [
                "value" => $this->report->performance()->uptimePercent()->decimal(),
                "formatted" => (string) $this->report->performance()->uptimePercent(),
            ],
            "average" => [
                "value" => $this->report->performance()->responseTimeMean()->value(),
                "formatted" => $this->report->performance()->responseTimeMean()->formatted(),
            ],
            "percentile" => [
                "percent" => [
                    "value" => $this->report->performance()->percentile()->percent()->decimal(),
                    "formatted" => (string) $this->report->performance()->percentile()->percent(),
                ],
                "average" => [
                    "value" => $percentileDuration->value(),
                    "formatted" => $percentileDuration->formatted(),
                ]
            ],
            "status" => $status
        ];
    }
}
