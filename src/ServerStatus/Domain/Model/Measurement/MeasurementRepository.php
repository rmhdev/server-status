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
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatusCollection;

interface MeasurementRepository
{
    /**
     * @param MeasurementId $id
     * @return null|Measurement
     */
    public function ofId(MeasurementId $id): ?Measurement;

    /**
     * @param Measurement|Measurement[] $measurement
     * @return MeasurementRepository
     */
    public function add($measurement): MeasurementRepository;

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
     * @param DateRange $dateRange
     * @return array
     */
    public function summaryValues(Check $check, DateRange $dateRange);

    /**
     * @return int
     */
    public function countAll(): int;

    /**
     * @param Check $check
     * @param DateRange $dateRange
     * @param Percent $percent
     * @return Percentile
     */
    public function findPercentile(Check $check, DateRange $dateRange, Percent $percent): Percentile;

    /**
     * @param Check $check
     * @param DateRange $dateRange
     * @return PerformanceStatusCollection
     */
    public function calculatePerformanceStatus(Check $check, DateRange $dateRange): PerformanceStatusCollection;
}
