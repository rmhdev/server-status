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

namespace ServerStatus\Tests\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementResult;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;

class MeasurementResultDataBuilder
{
    /**
     * @var MeasurementStatus
     */
    private $status;

    /**
     * @var MeasurementDuration
     */
    private $duration;

    /**
     * @var int
     */
    private $memory;

    public function __construct()
    {
        $this->status = MeasurementStatusDataBuilder::aMeasurementStatus()
            ->withCode(200)->withReasonPhrase("test")->build();
        $this->duration = MeasurementDurationDataBuilder::aMeasurementDuration()
            ->withDuration(100)->build();
        $this->memory = 0;
    }

    public function withStatus($status): MeasurementResultDataBuilder
    {
        if (is_numeric($status)) {
            $status = MeasurementStatusDataBuilder::aMeasurementStatus()->withCode($status)->build();
        }
        $this->status = $status;

        return $this;
    }

    public function withDuration($duration): MeasurementResultDataBuilder
    {
        if (is_numeric($duration)) {
            $duration = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration($duration)->build();
        }
        $this->duration = $duration;

        return $this;
    }

    public function withMemory(int $memory): MeasurementResultDataBuilder
    {
        $this->memory = $memory;

        return $this;
    }

    public function build(): MeasurementResult
    {
        return new MeasurementResult($this->status, $this->duration, $this->memory);
    }

    public static function aMeasurementResult(): MeasurementResultDataBuilder
    {
        return new self();
    }
}
