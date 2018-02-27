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
    /**
     * @var float
     */
    private $decimal;


    public function __construct(float $value = 0)
    {
        $this->decimal = $value;
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
        return sprintf("%s%%", round($this->percentage(), 2));
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
