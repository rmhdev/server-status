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

namespace ServerStatus\Tests\Model\Check;

use ServerStatus\Model\Measurement\Measurement;
use ServerStatus\Model\Measurement\MeasurementId;
use ServerStatus\Tests\Model\Measurement\MeasurementIdDataBuilder;

class MeasurementDataBuilder
{
    private $id;

    public function __construct()
    {
        $this->id = MeasurementIdDataBuilder::aMeasurementId()->build();
    }

    public function withId(MeasurementId $id): MeasurementDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function build(): Measurement
    {
        return new Measurement($this->id);
    }

    public static function aMeasurement(): MeasurementDataBuilder
    {
        return new self();
    }
}
