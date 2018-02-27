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

class MeasurementResult
{
    const STATUS_CODE_CLASS_SUCCESSFUL = 2;

    private $status;
    private $duration;
    private $memory;

    /**
     * @param MeasurementStatus $status The response status
     * @param MeasurementDuration $duration The duration
     * @param int $memory The memory usage (in bytes)
     */
    public function __construct(MeasurementStatus $status, MeasurementDuration $duration, int $memory = 0)
    {
        $this->assertMemory($memory);
        $this->status = $status;
        $this->duration = $duration;
        $this->memory = $memory;
    }

    private function assertMemory(int $memory): void
    {
        if (0 > $memory) {
            throw new \UnexpectedValueException(sprintf(
                'Memory should not be negative, "%s" bytes received',
                $memory
            ));
        }
    }

    public function statusCode(): int
    {
        return $this->status()->code();
    }

    public function status(): MeasurementStatus
    {
        return $this->status;
    }

    public function duration(): MeasurementDuration
    {
        return $this->duration;
    }

    /**
     * @return int The memory usage (in bytes)
     */
    public function memory(): int
    {
        return $this->memory;
    }
}
