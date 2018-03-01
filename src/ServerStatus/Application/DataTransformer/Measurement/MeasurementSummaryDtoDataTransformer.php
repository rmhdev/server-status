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

use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;

final class MeasurementSummaryDtoDataTransformer implements MeasurementSummaryDataTransformer
{
    private $measureSummary;

    public function write(MeasureSummary $measureSummary)
    {
        $this->measureSummary = $measureSummary;
    }

    private function measureSummary(): MeasureSummary
    {
        return $this->measureSummary;
    }

    public function read()
    {
        $data = [
            "from" => $this->measureSummary()->dateRange()->from()->format(DATE_ISO8601),
            "to" => $this->measureSummary()->dateRange()->to()->format(DATE_ISO8601),
            "minutes_grouped_by" => $this->measureSummary()->groupedByMinutes(),
            "name" => $this->measureSummary()->name(),
            "averages" => []
        ];
        foreach ($this->measureSummary()->averages() as $average) {
            $data["averages"][] = [
                "from" => $average->dateRange()->from()->format(DATE_ISO8601),
                "to" => $average->dateRange()->to()->format(DATE_ISO8601),
                "response_time" => $average->responseTime()
            ];
        }

        return $data;
    }
}
