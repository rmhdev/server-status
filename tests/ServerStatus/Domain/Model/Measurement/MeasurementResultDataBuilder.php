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

use ServerStatus\Domain\Model\Measurement\MeasurementResult;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;

class MeasurementResultDataBuilder
{
    private $status;
    private $statusCode;
    private $reasonPhrase;
    private $duration;
    private $memory;

    public function __construct()
    {
        $this->status = MeasurementStatusDataBuilder::aMeasurementStatus()
            ->withCode(200)->withReasonPhrase("test")->build();
        $this->duration = 0;
        $this->memory = 0;
    }

    public function withStatus(MeasurementStatus $status): MeasurementResultDataBuilder
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @deprecated
     */
    public function withStatusCode(int $statusCode): MeasurementResultDataBuilder
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @deprecated
     */
    public function withReasonPhrase(string $reasonPhrase): MeasurementResultDataBuilder
    {
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    public function withDuration(float $duration): MeasurementResultDataBuilder
    {
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
