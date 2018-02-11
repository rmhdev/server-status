<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Domain\Model\Measurement\Summary;

class SummaryAverage
{
    const FIELD_RESPONSE_TIME = "response_time";

    private $values;

    public function __construct($values = [])
    {
        if ($values) {
            if (array_intersect(array_keys($values), self::fields())) {
                $values = [$values];
            }
        }
        $this->values = $values;
    }

    private function values(): array
    {
        return $this->values;
    }

    public function responseTime(): float
    {
        $responseTimes = $this->responseTimes();
        if (!$responseTimes) {
            return 0;
        }

        return array_sum($responseTimes) / sizeof($responseTimes);
    }

    /**
     * @return float[]
     */
    private function responseTimes(): array
    {
        return array_map(function ($value) {
            return $value[self::FIELD_RESPONSE_TIME];
        }, $this->filterValuesWithResponseTime());
    }

    private function filterValuesWithResponseTime()
    {
        return array_filter($this->values(), function ($value) {
            return (array_key_exists(self::FIELD_RESPONSE_TIME, $value));
        });
    }

    /**
     * @return string[]
     */
    public static function fields(): array
    {
        return [
            self::FIELD_RESPONSE_TIME
        ];
    }
}
