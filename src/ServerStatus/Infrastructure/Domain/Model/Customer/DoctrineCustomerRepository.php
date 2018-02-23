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
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;

/**
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineCustomerRepository extends EntityRepository implements CustomerRepository
{
    public function ofId(CustomerId $id): ?Customer
    {
        // TODO: Implement ofId() method.
    }

    public function add(Customer $customer): CustomerRepository
    {
        // TODO: Implement add() method.
    }

    public function remove(Customer $customer): CustomerRepository
    {
        // TODO: Implement remove() method.
    }

    public function nextId(): CustomerId
    {
        // TODO: Implement nextId() method.
    }
}
