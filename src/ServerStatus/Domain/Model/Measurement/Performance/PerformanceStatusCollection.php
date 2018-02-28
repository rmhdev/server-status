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

namespace ServerStatus\Domain\Model\Measurement\Performance;

use ServerStatus\Domain\Model\Measurement\MeasurementStatus;

final class PerformanceStatusCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var MeasurementStatus[]
     */
    private $values;


    public function __construct($values = [])
    {
        $this->values = $this->processValues($values);
    }

    private function processValues($values = []): array
    {
        if (!is_iterable($values)) {
            $values = [$values];
        }
        $processed = [];
        foreach ($values as $value) {
            $this->assertPerformanceStatus($value);
            $processed[] = $value;
        }

        return $processed;
    }

    private function assertPerformanceStatus($performanceStatus)
    {
        if (!is_object($performanceStatus) || !$performanceStatus instanceof PerformanceStatus) {
            throw new \UnexpectedValueException(sprintf(
                'PerformanceStatusCollection only accepts "PerformanceStatus" objects, "%s" received',
                gettype($performanceStatus)
            ));
        }
    }

    public function count(): int
    {
        return sizeof($this->values);
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->values);
    }
}
