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

class MeasurementResultDataBuilder
{
    private $code;
    private $duration;
    private $memory;

    public function __construct()
    {
        $this->code = 200;
        $this->duration = 0;
        $this->memory = 0;
    }

    public function withCode(int $code): MeasurementResultDataBuilder
    {
        $this->code = $code;

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
        return new MeasurementResult($this->code, $this->duration, $this->memory);
    }

    public static function aMeasurementResult(): MeasurementResultDataBuilder
    {
        return new self();
    }
}
