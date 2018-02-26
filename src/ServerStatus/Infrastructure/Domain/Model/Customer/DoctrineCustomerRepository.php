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

namespace ServerStatus\Infrastructure\Domain\Model\Customer;

use Doctrine\ORM\EntityRepository;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;

/**
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineCustomerRepository extends EntityRepository implements CustomerRepository
{
    public function ofId(CustomerId $id): ?Customer
    {
        return $this->findOneBy(["id" => $id]);
    }

    public function add(Customer $customer): CustomerRepository
    {
        $this->getEntityManager()->persist($customer);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function remove(Customer $customer): CustomerRepository
    {
        $this->getEntityManager()->remove($customer);
        $this->getEntityManager()->flush();

        return $this;
    }

    public function nextId(): CustomerId
    {
        return new CustomerId();
    }

    public function ofEmail(CustomerEmail $email): ?Customer
    {
        return $this->findOneBy(["email" => $email]);
    }
}
