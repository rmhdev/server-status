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
use Doctrine\ORM\Query;
use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeMonth;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeWeek;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeYear;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementCollection;
use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;
use ServerStatus\Domain\Model\Measurement\Percentile\Percent;
use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatus;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatusCollection;
use ServerStatus\Domain\Model\Measurement\Summary\SummaryAverage;

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

    public function summaryValues(Check $check, DateRange $dateRange): array
    {
        $qb = $this->createQueryBuilder("a");
        // example date: "2018-01-01 00:00:00"
        switch ($dateRange->name()) {
            case DateRangeYear::NAME:
                $substring = 10; // until day number
                $fillWith = " 00:00:00";
                break;
            case DateRangeMonth::NAME:
            case DateRangeWeek::NAME:
                $substring = 14; // until hour number
                $fillWith = ":00:00";
                break;
            default:
                $substring = 17; // until first number of minute
                $fillWith = ":00";
        }

        $qb
            ->select($qb->expr()->substring("a.dateCreated", 0, $substring) . " as date")
            ->addSelect($qb->expr()->avg("a.result.duration.time") . " as duration")
            ->addSelect($qb->expr()->count("a.id") . " as total")
            ->where("a.check = :check")
            ->andWhere("a.dateCreated >= :from")
            ->andWhere("a.dateCreated < :to")
            ->orderBy("a.result.duration.time", "ASC")
            ->groupBy("date")
            ->setParameters([
                "check" => $check,
                'from' => $dateRange->from(),
                'to' => $dateRange->to(),
            ]);
        $values = [];
        foreach ($qb->getQuery()->execute() as $item) {
            $values[] = [
                "date" => $item["date"] . $fillWith,
                "count" => $item["total"],
                SummaryAverage::FIELD_RESPONSE_TIME => $item["duration"],
            ];
        }

        return $values;
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
            ->orderBy("a.result.duration.time", "ASC")
                
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
            ->select($qb->expr()->count("a.id") . " as total")
            ->addSelect($qb->expr()->avg("a.result.duration.time") . " as duration_time")
            ->addSelect("a.result.status.code as status_code")
            ->groupBy("status_code")
            ->where("a.check = :check")
            ->andWhere("a.dateCreated >= :from")
            ->andWhere("a.dateCreated < :to")
            ->setParameters([
                'check' => $check,
                'from' => $dateRange->from(),
                'to' => $dateRange->to(),
            ])
        ;
        $measurements = $qb->getQuery()->setHydrationMode(Query::HYDRATE_ARRAY)->execute();
        $performanceStatuses = [];
        foreach ($measurements as $measurementValue) {
            $performanceStatuses[] = new PerformanceStatus(
                new MeasurementStatus((int) $measurementValue["status_code"]),
                new MeasurementDuration((float) $measurementValue["duration_time"]),
                (int) $measurementValue["total"]
            );
        }

        return new PerformanceStatusCollection($performanceStatuses);
    }

    public function findErrors(Alert $alert, \DateTimeInterface $dateTime): MeasurementCollection
    {
        return new MeasurementCollection();
    }
}
