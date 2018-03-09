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
            "name" => $this->measureSummary()->name(),
            "date_range" => [
                "from" => $this->measureSummary()->dateRange()->from()->format(DATE_ISO8601),
                "to" => $this->measureSummary()->dateRange()->to()->format(DATE_ISO8601),
                "name" => $this->measureSummary()->dateRange()->name(),
                "formatted" => $this->measureSummary()->dateRange()->formatted(),
            ],
            "averages" => []
        ];
        foreach ($this->measureSummary()->averages() as $average) {
            $data["averages"][] = [
                "date_range" => [
                    "from" => $average->dateRange()->from()->format(DATE_ISO8601),
                    "to" => $average->dateRange()->to()->format(DATE_ISO8601),
                    "name" => $average->dateRange()->name(),
                    "formatted" => $average->dateRange()->formatted(),
                ],
                "response_time" => [
                    "value" => $average->responseTime()->value(),
                    "formatted" => $average->responseTime()->formatted(),
                ]
            ];
        }

        return $data;
    }
}
