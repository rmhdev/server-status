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

    public function __construct()
    {
        $this->totalMeasurements = 0;
        $this->successfulMeasurements = 0;
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


    public function build(): Performance
    {
        return new Performance($this->totalMeasurements, $this->successfulMeasurements);
    }

    public static function aPerformance(): PerformanceDataBuilder
    {
        return new self();
    }
}
