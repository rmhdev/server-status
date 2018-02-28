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

use ServerStatus\Domain\Model\Measurement\MeasurementStatus;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatus;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementStatusDataBuilder;

class PerformanceStatusDataBuilder
{
    /**
     * @var MeasurementStatus
     */
    private $status;

    /**
     * @var int
     */
    private $count;


    public function __construct()
    {
        $this->status = MeasurementStatusDataBuilder::aMeasurementStatus()->build();
        $this->count = 0;
    }

    public function withStatus(MeasurementStatus $status): PerformanceStatusDataBuilder
    {
        $this->status = $status;

        return $this;
    }

    public function withCount($values): PerformanceStatusDataBuilder
    {
        $this->count = $values;

        return $this;
    }

    public function build(): PerformanceStatus
    {
        return new PerformanceStatus($this->status, $this->count);
    }

    public static function aPerformanceStatus(): PerformanceStatusDataBuilder
    {
        return new self();
    }
}
