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

namespace ServerStatus\Domain\Model\Common\DateRange;

final class DateRangeFactory
{
    private static $dateRanges = [
        DateRangeYear::class,
        DateRangeMonth::class,
        DateRangeWeek::class,
        DateRangeDay::class,
        DateRangeLast24Hours::class,
    ];

    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_map(function ($class) {
            return $class::NAME;
        }, self::$dateRanges);
    }

    public static function create(string $name, \DateTimeInterface $dateTime): DateRange
    {
        if ("" === $name) {
            throw new \UnexpectedValueException('You must indicate the name of the DateRange, empty value received');
        }
        foreach (self::$dateRanges as $dateRangeClass) {
            if ($name === $dateRangeClass::NAME) {
                return new $dateRangeClass($dateTime);
            }
        }

        throw new \UnexpectedValueException(sprintf('DateRange name "" is unknown', $name));
    }
}
