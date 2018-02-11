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
            "from" => $this->measureSummary()->from()->format(DATE_ISO8601),
            "to" => $this->measureSummary()->to()->format(DATE_ISO8601),
            "name" => $this->measureSummary()->name(),
            "averages" => []
        ];
        foreach ($this->measureSummary()->averages() as $average) {
            $data["averages"][] = [
                "from" => $average->from()->format(DATE_ISO8601),
                "to" => $average->to()->format(DATE_ISO8601),
                "response_time" => $average->responseTime()
            ];
        }

        return $data;
    }
}
