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

namespace ServerStatus\Tests\Domain\Model\Measurement\Performance;

use ServerStatus\Domain\Model\Measurement\Performance\Performance;

class PerformanceDataBuilder
{
    /**
     * @var int
     */
    private $totalMeasurements;

    /**
     * @var int
     */
    private $successfulMeasurements;

    /**
     * @var array
     */
    private $means;

    public function __construct()
    {
        $this->totalMeasurements = 0;
        $this->successfulMeasurements = 0;
        $this->means = [];
    }

    public function withTotalMeasurements($totalMeasurements): PerformanceDataBuilder
    {
        $this->totalMeasurements = $totalMeasurements;

        return $this;
    }

    public function withSuccessfulMeasurements($successfulMeasurements): PerformanceDataBuilder
    {
        $this->successfulMeasurements = $successfulMeasurements;

        return $this;
    }

    public function withMeans($means): PerformanceDataBuilder
    {
        $this->means = $means;

        return $this;
    }

    public function build(): Performance
    {
        return new Performance($this->totalMeasurements, $this->successfulMeasurements, $this->means);
    }

    public static function aPerformance(): PerformanceDataBuilder
    {
        return new self();
    }
}
