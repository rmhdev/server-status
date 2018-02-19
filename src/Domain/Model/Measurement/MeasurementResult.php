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

    private $code;
    private $duration;
    private $memory;

    public function __construct(int $code, float $duration = 0, int $memory = 0)
    {
        $this->assertDuration($duration);
        $this->assertMemory($memory);
        $this->code = $code;
        $this->duration = $duration;
        $this->memory = $memory;
    }

    private function assertDuration(float $duration): void
    {
        if (0 > $duration) {
            throw new \UnexpectedValueException(sprintf(
                'Duration should not be negative, "%s" seconds received',
                $duration
            ));
        }
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

    public function code(): int
    {
        return $this->code;
    }

    public function isSuccessful(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_SUCCESSFUL);
    }

    private function isStatusCodeClass(int $classNumber): bool
    {
        return $classNumber === (int) substr((string) $this->code(), 0, 1);
    }

    public function duration(): float
    {
        return $this->duration;
    }

    public function memory(): int
    {
        return $this->memory;
    }
}
