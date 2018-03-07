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

namespace ServerStatus\Domain\Model\Measurement\Percentile;

final class Percent
{
    const MIN_VALUE = -1;
    const MAX_VALUE = 1;
    const FORMAT_PRECISION = 4;

    /**
     * @var float
     */
    private $decimal;


    /**
     * @param float $decimal the decimal value (usually between -1 and 1)
     */
    public function __construct(float $decimal = 0)
    {
        $this->decimal = $decimal;
    }

    public function decimal(): float
    {
        return $this->decimal;
    }

    public function percentage(): float
    {
        return $this->decimal() * 100;
    }

    public function __toString(): string
    {
        return sprintf("%s%%", sprintf('%01.4f', round($this->percentage(), self::FORMAT_PRECISION)));
    }

    public static function createFromDecimalFraction(float $value): Percent
    {
        return new Percent($value);
    }

    public static function createFromPercentage(float $value): Percent
    {
        return new Percent($value / 100);
    }
}
