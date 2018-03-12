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

namespace ServerStatus\Infrastructure\Domain\Model\Alert;

use Doctrine\ORM\EntityRepository;
use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Alert\AlertCollection;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\Alert\AlertTimeWindow;
use ServerStatus\Domain\Model\Customer\CustomerId;

/**
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineAlertRepository extends EntityRepository implements AlertRepository
{
    public function ofId(AlertId $id): ?Alert
    {
        return $this->findOneBy(["id" => $id]);
    }

    public function add(Alert $alert): AlertRepository
    {
        $this->getEntityManager()->persist($alert);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function remove(Alert $alert): AlertRepository
    {
        $this->getEntityManager()->remove($alert);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function nextId(): AlertId
    {
        return new AlertId();
    }

    public function byCustomer(CustomerId $id): AlertCollection
    {
        // TODO: Implement byCustomer() method.
    }

    public function enabled(AlertTimeWindow $window = null): AlertCollection
    {
        // TODO: Implement enabled() method.
    }
}
