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

use Doctrine\ORM\EntityRepository;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

/**
 * @method Measurement|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineMeasurementRepository extends EntityRepository implements MeasurementRepository
{
    const BATCH_SIZE = 5000;

    public function ofId(MeasurementId $id): ?Measurement
    {
        return $this->findOneBy(["id" => $id]);
    }

    public function add($measurement): MeasurementRepository
    {
        if (!is_iterable($measurement)) {
            $measurement = [$measurement];
        }
        foreach ($measurement as $i => $item) {
            $this->assertAddMeasurement($item);
            $this->getEntityManager()->persist($item);
            if (0 === ($i % self::BATCH_SIZE)) {
                $this->getEntityManager()->flush();
            }
        }
        $this->getEntityManager()->flush();

        return $this;
    }

    private function assertAddMeasurement($measurement)
    {
        if (!is_object($measurement)) {
            throw new \UnexpectedValueException(sprintf(
                'Only Measurement objects can be added to repository, "%s" received',
                gettype($measurement)
            ));
        }
        if (!$measurement instanceof Measurement) {
            throw new \UnexpectedValueException(sprintf(
                'Only Measurement objects can be added to repository, "%s" received',
                get_class($measurement)
            ));
        }
    }

    public function remove(Measurement $measurement): MeasurementRepository
    {
        $this->getEntityManager()->remove($measurement);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function nextId(): MeasurementId
    {
        return new MeasurementId();
    }

    public function summaryByMinute(Check $check, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return [];
    }

    public function summaryByHour(Check $check, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return [];
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder("a");

        return $qb
            ->select($qb->expr()->count("a.id"))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
