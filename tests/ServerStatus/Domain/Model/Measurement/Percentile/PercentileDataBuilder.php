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

namespace ServerStatus\Tests\Domain\Model\Measurement\Percentile;

use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;

final class PercentileDataBuilder
{
    /**
     * @var Percent
     */
    private $percent;

    /**
     * @var float
     */
    private $value;

    public function __construct()
    {
        $this->percent = PercentDataBuilder::aPercent()->build();
        $this->value = 0;
    }

    public function withPercent($percent): PercentileDataBuilder
    {
        if (is_numeric($percent)) {
            $percent = PercentDataBuilder::aPercent()->withValue($percent)->build();
        }
        $this->percent = $percent;

        return $this;
    }

    public function withValue($value): PercentileDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build()
    {
        return new Percentile($this->percent, $this->value);
    }

    public static function aPercentile(): PercentileDataBuilder
    {
        return new self();
    }
}
