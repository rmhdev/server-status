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

namespace ServerStatus\ServerStatus\Domain\Model\Measurement\Percentile;

use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;

final class PercentileCalculator
{
    private $values;

    public function __construct($values = [])
    {
        $this->values = $this->processValues($values);
    }

    private function processValues($values = [])
    {
        if (!is_iterable($values)) {
            $values = [$values];
        }
        if (false === sort($values, SORT_NUMERIC)) {
            throw new \UnexpectedValueException('Values cannot be sorted');
        }

        return $values;
    }

    public function percentile(Percent $percent): Percentile
    {
        $size = sizeof($this->values);
        if (!$size) {
            return new Percentile($percent, 0);
        }
        $position = (int) floor($percent->decimal() * $size);
        $value = $this->values[$position];

        return new Percentile($percent, $value);
    }
}
