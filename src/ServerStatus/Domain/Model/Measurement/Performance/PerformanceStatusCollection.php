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

final class PerformanceStatusCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var PerformanceStatus[]
     */
    private $performanceStatuses;


    public function __construct($performanceStatuses = [])
    {
        $this->performanceStatuses = $this->processPerformanceStatuses($performanceStatuses);
    }

    private function processPerformanceStatuses($performanceStatuses = []): array
    {
        if (!is_iterable($performanceStatuses)) {
            $performanceStatuses = [$performanceStatuses];
        }
        $processed = [];
        foreach ($performanceStatuses as $performanceStatus) {
            $this->assertPerformanceStatus($performanceStatus);
            $processed[] = $performanceStatus;
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
        return sizeof($this->performanceStatuses);
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->performanceStatuses);
    }
}
