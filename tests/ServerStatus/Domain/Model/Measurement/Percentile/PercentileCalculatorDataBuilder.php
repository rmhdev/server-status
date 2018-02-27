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

use ServerStatus\ServerStatus\Domain\Model\Measurement\Percentile\PercentileCalculator;

final class PercentileCalculatorDataBuilder
{
    /**
     * @var float[]
     */
    private $values;

    public function __construct()
    {
        $this->values = [];
    }

    public function withValues($values): PercentileCalculatorDataBuilder
    {
        $this->values = $values;

        return $this;
    }

    public function build()
    {
        return new PercentileCalculator($this->values);
    }

    public static function aPercentileCalculator(): PercentileCalculatorDataBuilder
    {
        return new self();
    }
}
