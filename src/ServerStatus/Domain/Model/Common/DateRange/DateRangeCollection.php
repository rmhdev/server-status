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

final class DateRangeCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var DateRange[]
     */
    private $dateRanges;

    public function __construct($dateRanges = [])
    {
        $this->dateRanges = $this->processValues($dateRanges);
    }

    private function processValues($dateRanges = []): array
    {
        if (!\is_iterable($dateRanges)) {
            $dateRanges = [$dateRanges];
        }
        $values = [];
        foreach ($dateRanges as $dateRange) {
            $this->assertDateRange($dateRange);
            $values[] = $dateRange;
        }

        return $values;
    }

    private function assertDateRange($dateRange)
    {
        if (!is_object($dateRange)) {
            throw new \UnexpectedValueException(sprintf(
                'Collection only accepts "DateRange" objects, "%s" received',
                $dateRange
            ));
        }
        if (!$dateRange instanceof DateRange) {
            throw new \UnexpectedValueException(sprintf(
                'Collection only accepts "DateRange" objects, "%s" received',
                get_class($dateRange)
            ));
        }
    }

    private function dateRanges(): \ArrayIterator
    {
        return new \ArrayIterator($this->dateRanges);
    }

    public function count(): int
    {
        return $this->dateRanges()->count();
    }

    public function getIterator(): \Iterator
    {
        return $this->dateRanges();
    }
}
