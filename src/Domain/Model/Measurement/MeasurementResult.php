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

    private $statusCode;
    private $reasonPhrase;
    private $duration;
    private $memory;

    /**
     * @param integer $statusCode The response status Code
     * @param string $reasonPhrase The response reason phrase
     * @param float $duration The duration (in milliseconds)
     * @param int $memory The memory usage (in bytes)
     */
    public function __construct(int $statusCode, string $reasonPhrase = "", float $duration = 0, int $memory = 0)
    {
        $this->assertDuration($duration);
        $this->assertMemory($memory);
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        $this->duration = $duration;
        $this->memory = $memory;
    }

    private function assertDuration(float $duration): void
    {
        if (0 > $duration) {
            throw new \UnexpectedValueException(sprintf(
                'Duration should not be negative, "%s" milliseconds received',
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

    /**
     * @return int The response status code
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function reasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function isSuccessful(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_SUCCESSFUL);
    }

    private function isStatusCodeClass(int $classNumber): bool
    {
        return $classNumber === (int) substr((string) $this->statusCode(), 0, 1);
    }

    /**
     * @return float The duration (in milliseconds)
     */
    public function duration(): float
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
