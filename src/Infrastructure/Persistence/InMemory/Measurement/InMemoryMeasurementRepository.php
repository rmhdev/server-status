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

namespace ServerStatus\Infrastructure\Persistence\InMemory\Measurement;

use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementDoesNotExistException;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

class InMemoryMeasurementRepository implements MeasurementRepository
{
    private $measurements;

    public function __construct()
    {
        $this->measurements = [];
    }

    /**
     * @inheritdoc
     */
    public function ofId(MeasurementId $id): ?Measurement
    {
        if (!array_key_exists($id->value(), $this->measurements)) {
            return null;
        }

        return $this->measurements[$id->value()];
    }

    /**
     * @inheritdoc
     */
    public function add(Measurement $measurement): MeasurementRepository
    {
        $this->measurements[$measurement->id()->value()] = $measurement;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Measurement $measurement): MeasurementRepository
    {
        $id = $measurement->id()->value();
        if (!array_key_exists($id, $this->measurements)) {
            throw new MeasurementDoesNotExistException(
                sprintf('Measurement "%s" is not in the repository', $id)
            );
        }
        unset($this->measurements[$id]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): MeasurementId
    {
        return new MeasurementId();
    }
}
