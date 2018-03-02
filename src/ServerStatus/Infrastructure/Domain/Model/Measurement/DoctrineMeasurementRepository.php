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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatus;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatusCollection;

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

    public function summaryByMinute(Check $check, DateRange $dateRange)
    {
        return [];
    }

    public function summaryByHour(Check $check, DateRange $dateRange)
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

    public function findPercentile(Check $check, DateRange $dateRange, Percent $percent): Percentile
    {
        // Total number of measurements for Check in date range:
        $count = $this->countByCheckAndDateRange($check, $dateRange);
        if (0 === $count) {
            return new Percentile($percent, 0);
        }
        $percentileIndex = round($count * $percent->decimal());

        // Measurement for a given position
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select("a")
            ->where("a.check = :check")
            ->andWhere("a.dateCreated >= :from")
            ->andWhere("a.dateCreated < :to")
            ->orderBy("a.dateCreated", "ASC") // TODO: it should be ordered by duration!!!
            ->setFirstResult($percentileIndex)
            ->setMaxResults(1)
            ->setParameters([
                'check' => $check,
                'from' => $dateRange->from(),
                'to' => $dateRange->to(),
            ]);

        $measurements = $qb->getQuery()->execute();
        $duration = 0;
        if ($measurements) {
            /* @var Measurement[] $measurements */
            $duration = $measurements[0]->result()->duration()->value();
        }

        return new Percentile($percent, $duration);
    }

    private function countByCheckAndDateRange(Check $check, DateRange $dateRange): float
    {
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select($qb->expr()->count("a.id"))
            ->where("a.check = :check")
            ->andWhere("a.dateCreated >= :from")
            ->andWhere("a.dateCreated < :to")
            ->setParameters([
                'check' => $check,
                'from' => $dateRange->from(),
                'to' => $dateRange->to(),
            ]);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function calculatePerformanceStatus(Check $check, DateRange $dateRange): PerformanceStatusCollection
    {
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select("a")
            //->select($qb->expr()->count("a.id"))
            //->addSelect("a.result.duration as duration")
            //->addSelect("a.result.status as status")
            //->groupBy("duration")
            //->addGroupBy("status")

            ->where("a.check = :check")
            ->andWhere("a.dateCreated >= :from")
            ->andWhere("a.dateCreated < :to")
            ->setParameters([
                'check' => $check,
                'from' => $dateRange->from(),
                'to' => $dateRange->to(),
            ])
        ;

        $values = [];
        $durations = [];
        $measurements = $qb->getQuery()->setHydrationMode(Query::HYDRATE_ARRAY)->execute();
        foreach ($measurements as $measurement) {
            $code = $measurement["result.status.code"];
            if (!array_key_exists($code, $values)) {
                $values[$code] = [
                    "status" => $code,
                    "count" => 0,
                    "duration" => 0,
                ];
            }
            $values[$code]["count"] += 1;
            $values[$code]["duration"] += $measurement["result.duration.time"];

            $durations[] = $measurement["result.duration.time"];
        }
        $performanceStatuses = [];
        foreach ($values as $code => $value) {
            $avgDuration = $values[$code]["duration"] / $values[$code]["count"];
            $performanceStatuses[] = new PerformanceStatus(
                new MeasurementStatus($code),
                new MeasurementDuration($avgDuration),
                $values[$code]["count"]
            );
        }

        return new PerformanceStatusCollection($performanceStatuses);
    }
}
