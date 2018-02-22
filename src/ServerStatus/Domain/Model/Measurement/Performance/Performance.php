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

final class Performance
{
    const UPTIME_PERCENT_PRECISION = 4;

    /**
     * @var int
     */
    private $totalMeasurements;

    /**
     * @var int
     */
    private $successfulMeasurements;

    public function __construct(int $totalMeasurements, int $successfulMeasurements)
    {
        $this->totalMeasurements = $totalMeasurements;
        $this->successfulMeasurements = $successfulMeasurements;
    }

    public function totalMeasurements(): int
    {
        return $this->totalMeasurements;
    }

    public function successfulMeasurements(): int
    {
        return $this->successfulMeasurements;
    }

    public function uptimePercent(): float
    {
        if (1 > $this->totalMeasurements()) {
            return round(0, self::UPTIME_PERCENT_PRECISION);
        }

        return round(
            $this->successfulMeasurements() / $this->totalMeasurements(),
            self::UPTIME_PERCENT_PRECISION
        );
    }
}
