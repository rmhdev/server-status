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

use ServerStatus\Domain\Model\Measurement\MeasurementId;

class MeasurementIdDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "loremipsum";
    }

    public function withValue(string $value): MeasurementIdDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): MeasurementId
    {
        return new MeasurementId($this->value);
    }

    public static function aMeasurementId(): MeasurementIdDataBuilder
    {
        return new MeasurementIdDataBuilder();
    }
}
