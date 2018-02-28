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

namespace ServerStatus\Domain\Model\Measurement;

final class MeasurementCollection implements \Countable, \IteratorAggregate
{
    private $measurements;

    public function __construct($measurements = [])
    {
        $this->measurements = $this->processMeasurements($measurements);
    }

    private function processMeasurements($measurements = [])
    {
        $measurements = is_iterable($measurements) ? $measurements : [$measurements];
        $processed = [];
        foreach ($measurements as $measurement) {
            $this->assertMeasurement($measurement);
            $processed[] = $measurement;
        }

        return $processed;
    }

    private function assertMeasurement($measurement)
    {
        if (!is_object($measurement) || !$measurement instanceof Measurement) {
            throw new \UnexpectedValueException(sprintf(
                'MeasurementCollection only accepts "Measurement" objects, "%s" received',
                gettype($measurement)
            ));
        }
    }

    public function count(): int
    {
        return sizeof($this->measurements);
    }

    /**
     * @return \Iterator|Measurement[]
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->measurements);
    }
}
