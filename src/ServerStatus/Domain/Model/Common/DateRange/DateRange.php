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

interface DateRange
{
    public function from(): \DateTimeImmutable;

    public function to(): \DateTimeImmutable;

    public function name(): string;

    public function formatted(): string;

    public function isInBounds(\DateTimeInterface $dateTime): bool;

    public function next(): DateRange;

    public function previous(): DateRange;

    /**
     * @param DateRange $range
     * @return int &lt; 0 if $this is less than
     * $range; &gt; 0 if $this
     * is greater than $range, and 0 if they are
     * equal.
     */
    public function compareTo(DateRange $range): int;

    /**
     * Size of the interval for the given date range.
     * @return \DateInterval
     */
    public function interval(): \DateInterval;

    public function __toString(): string;
}
