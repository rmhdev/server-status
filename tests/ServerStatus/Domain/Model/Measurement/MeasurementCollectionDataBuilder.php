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

use ServerStatus\Domain\Model\Measurement\MeasurementCollection;

class MeasurementCollectionDataBuilder
{
    private $measurements;

    public function __construct()
    {
        $this->measurements = [];
    }

    public function withMeasurements($measurements = []): MeasurementCollectionDataBuilder
    {
        $this->measurements = $measurements;

        return $this;
    }

    public function build(): MeasurementCollection
    {
        return new MeasurementCollection($this->measurements);
    }

    public static function aMeasurementCollection(): MeasurementCollectionDataBuilder
    {
        return new self();
    }
}
