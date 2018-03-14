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

namespace ServerStatus\Infrastructure\Domain\Model\AlertNotification;

use Doctrine\ORM\EntityRepository;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\AlertNotification\AlertNotification;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationCollection;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationId;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;

/**
 * @method AlertNotification|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineAlertNotificationRepository extends EntityRepository implements AlertNotificationRepository
{
    public function ofId(AlertNotificationId $id): ?AlertNotification
    {
        return $this->findOneBy(["id" => $id]);
    }

    public function add(AlertNotification $notification): AlertNotificationRepository
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function remove(AlertNotification $notification): AlertNotificationRepository
    {
        $this->getEntityManager()->remove($notification);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function nextId(): AlertNotificationId
    {
        return new AlertNotificationId();
    }

    public function byAlert(AlertId $id, DateRange $dateRange): AlertNotificationCollection
    {
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select("a")
            ->leftJoin("a.alert", "alert")
            ->where("alert.id = :alertId")
            ->andWhere("a.createdAt >= :from")
            ->andWhere("a.createdAt < :to")
            ->orderBy("a.createdAt", "ASC")
            ->setParameters([
                "alertId" => $id,
                "from" => $dateRange->from(),
                "to" => $dateRange->to(),
            ])
        ;

        return new AlertNotificationCollection($qb->getQuery()->execute());
    }
}
