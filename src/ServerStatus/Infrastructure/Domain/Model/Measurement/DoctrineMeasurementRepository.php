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

namespace ServerStatus\Infrastructure\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

class DoctrineMeasurementRepository implements MeasurementRepository
{
    public function ofId(MeasurementId $id): ?Measurement
    {
        // TODO: Implement ofId() method.
    }

    public function add(Measurement $measurement): MeasurementRepository
    {
        // TODO: Implement add() method.
    }

    public function remove(Measurement $measurement): MeasurementRepository
    {
        // TODO: Implement remove() method.
    }

    public function nextId(): MeasurementId
    {
        // TODO: Implement nextId() method.
    }

    public function summaryByMinute(Check $check, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        // TODO: Implement summaryByMinute() method.
    }

    public function summaryByHour(Check $check, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        // TODO: Implement summaryByHour() method.
    }
}
