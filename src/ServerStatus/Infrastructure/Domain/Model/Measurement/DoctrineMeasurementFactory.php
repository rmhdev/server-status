<?php
declare(strict_types=1);

/**
 * This file is part of the bidaia package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Infrastructure\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementFactory;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementResult;

class DoctrineMeasurementFactory implements MeasurementFactory
{
    public function build(
        MeasurementId $id,
        \DateTimeInterface $dateCreated,
        Check $check,
        MeasurementResult $result
    ): Measurement {
        return new Measurement($id, $dateCreated, $check, $result);
    }
}
