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

use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;

final class PerformanceStatus
{
    /**
     * @var MeasurementStatus
     */
    private $status;

    /**
     * @var MeasurementDuration
     */
    private $durationAverage;

    /**
     * @var integer
     */
    private $count;

    public function __construct(MeasurementStatus $status, MeasurementDuration $durationAverage, int $count = 0)
    {
        $this->assertCount($count);
        $this->status = $status;
        $this->durationAverage = $durationAverage;
        $this->count = $count;
    }

    private function assertCount($count)
    {
        if (0 > $count) {
            throw new \UnexpectedValueException(sprintf(
                'Performance status count must be greater or equal than 0, "" received',
                $count
            ));
        }
    }

    public function status(): MeasurementStatus
    {
        return $this->status;
    }

    public function durationAverage(): MeasurementDuration
    {
        return $this->durationAverage;
    }

    public function count(): int
    {
        return $this->count;
    }
}
