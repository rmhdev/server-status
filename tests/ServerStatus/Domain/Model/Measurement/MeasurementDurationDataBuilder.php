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

class MeasurementDurationDataBuilder
{
    /**
     * @var float
     */
    private $duration;

    private function __construct()
    {
        $this->duration = 0;
    }

    public function withDuration($duration): MeasurementDurationDataBuilder
    {
        $this->duration = $duration;

        return $this;
    }

    public function build(): MeasurementDuration
    {
        return new MeasurementDuration($this->duration);
    }

    public static function aMeasurementDuration(): MeasurementDurationDataBuilder
    {
        return new self();
    }
}
