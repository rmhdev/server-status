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
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Check\CheckStatus;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerStatus;

/**
 * @method Check|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineCheckRepository extends EntityRepository implements CheckRepository
{
    /**
     * @inheritdoc
     */
    public function ofId(CheckId $id): ?Check
    {
        return $this->findOneBy(["id" => $id]);
    }

    /**
     * @inheritdoc
     */
    public function add(Check $check): CheckRepository
    {
        $this->getEntityManager()->persist($check);
        $this->getEntityManager()->flush();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Check $check): CheckRepository
    {
        $this->getEntityManager()->remove($check);
        $this->getEntityManager()->flush();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): CheckId
    {
        return new CheckId();
    }

    /**
     * @inheritdoc
     */
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

    public function byCustomerAndSlug(CustomerId $id, CheckName $slug): ?Check
    {
        return $this->createQueryBuilder("a")
            ->leftJoin("a.customer", "customer")
            ->where("customer.id = :id")
            ->andWhere("a.name.slug = :slug")
            ->setParameter("id", $id)
            ->setParameter("slug", $slug->slug())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritdoc
     */
    public function enabled(): CheckCollection
    {
        return new CheckCollection(
            $this->createQueryBuilder("a")
                ->where("a.status.code = :statusEnabled")
                ->andWhere("a.customer.status.code = :customerEnabled")
                ->setParameters([
                    'statusEnabled' => CheckStatus::CODE_ENABLED,
                    'customerEnabled' => CustomerStatus::CODE_ENABLED,
                ])
                ->getQuery()
                ->execute()
        );
    }
}
