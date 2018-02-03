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

namespace ServerStatus\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Check\Check;

interface MeasurementRepository
{
    /**
     * @param MeasurementId $id
     * @return null|Measurement
     */
    public function ofId(MeasurementId $id): ?Measurement;

    /**
     * @param Measurement $measurement
     * @return MeasurementRepository
     */
    public function add(Measurement $measurement): MeasurementRepository;

    /**
     * @param Measurement $measurement
     * @return MeasurementRepository
     * @throws \ServerStatus\Domain\Model\Measurement\MeasurementDoesNotExistException
     */
    public function remove(Measurement $measurement): MeasurementRepository;

    /**
     * @return MeasurementId
     */
    public function nextId(): MeasurementId;

    /**
     * @param Check $check
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     * @return array
     */
    public function summaryByMinute(Check $check, \DateTimeInterface $from, \DateTimeInterface $to);

    /**
     * @param Check $check
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     * @return array
     */
    public function summaryByHour(Check $check, \DateTimeInterface $from, \DateTimeInterface $to);
}
