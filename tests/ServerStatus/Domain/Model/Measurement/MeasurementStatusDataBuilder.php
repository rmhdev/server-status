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

use ServerStatus\Domain\Model\Measurement\MeasurementStatus;

class MeasurementStatusDataBuilder
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $reasonPhrase;

    private function __construct()
    {
        $this->statusCode = 200;
        $this->reasonPhrase = "test phrase";
    }

    public function withCode($statusCode): MeasurementStatusDataBuilder
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function withReasonPhrase($reasonPhrase): MeasurementStatusDataBuilder
    {
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    public function build(): MeasurementStatus
    {
        return new MeasurementStatus($this->statusCode, $this->reasonPhrase);
    }

    public static function aMeasurementStatus(): MeasurementStatusDataBuilder
    {
        return new self();
    }
}
