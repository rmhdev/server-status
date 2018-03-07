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
     * @param float $value the percent value (decimal between -1 and 1)
     */
    public function __construct(float $value = 0)
    {
        $this->assertValue($value);
        $this->decimal = $value;
    }

    private function assertValue($value)
    {
        if (self::MIN_VALUE > $value || self::MAX_VALUE < $value) {
            throw new \UnexpectedValueException(
                sprintf('Value must be between %d and %s, "%s" received', self::MIN_VALUE, self::MAX_VALUE, $value)
            );
        }
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
        return sprintf("%s%%", round($this->percentage(), self::FORMAT_PRECISION));
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
