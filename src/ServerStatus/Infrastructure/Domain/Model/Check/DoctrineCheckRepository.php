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

namespace ServerStatus\Infrastructure\Domain\Model\Check;

use Doctrine\ORM\EntityRepository;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckCollection;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\CustomerId;

/**
 * @method Check|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineCheckRepository extends EntityRepository implements CheckRepository
{
    public function ofId(CheckId $id): ?Check
    {
        return $this->findOneBy(["id" => $id]);
    }

    public function add(Check $check): CheckRepository
    {
        $this->getEntityManager()->persist($check);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function remove(Check $check): CheckRepository
    {
        $this->getEntityManager()->remove($check);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function nextId(): CheckId
    {
        return new CheckId();
    }

    public function byCustomer(CustomerId $id): CheckCollection
    {
        return new CheckCollection(
            $this->createQueryBuilder("a")
                ->leftJoin("a.customer", "customer")
                ->where("customer.id = :id")
                ->setParameter("id", $id)
                ->getQuery()
                ->execute()
        );
    }
}
