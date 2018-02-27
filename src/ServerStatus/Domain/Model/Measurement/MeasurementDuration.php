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

class MeasurementDuration
{
    private $time;

    /**
     * @param float $duration  Duration in milliseconds
     */
    public function __construct(float $duration)
    {
        $this->assertDuration($duration);
        $this->time = $duration;
    }

    private function assertDuration($duration)
    {
        if (0 > $duration) {
            throw new \UnexpectedValueException(
                sprintf('Duration cannot be negative, "%s" received', $duration)
            );
        }
    }

    private function milliseconds(): float
    {
        return $this->time;
    }

    public function formatted(): string
    {
        $ms = abs($this->milliseconds());
        // 1 second
        if (1000 > $ms) {
            return sprintf("%d ms", $ms);
        }

        // 1 minute
        if (60000 > $ms) {
            $seconds = round($ms / 1000, 2);

            return sprintf("%s second%s", $seconds, $seconds > 1 ? 's' : '');
        }

        $seconds = floor($ms / 1000);
        // 1 hour
        if (3600 > $seconds) {
            $minutes = floor($seconds / 60);
            $seconds = $seconds % 60;
            if (0 == $seconds) {
                return sprintf("%s minute%s", $minutes, $minutes > 1 ? 's' : '');
            }
            return sprintf("%dm %02ds", $minutes, $seconds);
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds - ($hours * 3600)) / 60);
        $seconds = $seconds - ($hours * 3600) - ($minutes * 60);
        if (0 == $minutes && 0 == $seconds) {
            return sprintf("%s hour%s", $hours, $hours > 1 ? 's' : '');
        }

        return sprintf("%dh %02dm %02ds", $hours, $minutes, $seconds);
    }
}
