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

    public function __construct()
    {
        $this->code = 200;
    }

    public function withCode(int $code): MeasurementResultDataBuilder
    {
        $this->code = $code;

        return $this;
    }

    public function build(): MeasurementResult
    {
        return new MeasurementResult($this->code);
    }

    public static function aMeasurementResult(): MeasurementResultDataBuilder
    {
        return new self();
    }
}
