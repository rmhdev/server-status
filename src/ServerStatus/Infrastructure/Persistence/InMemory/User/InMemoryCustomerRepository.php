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

namespace ServerStatus\Infrastructure\Persistence\InMemory\User;

use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Domain\Model\Customer\Customer;

class InMemoryCustomerRepository implements CustomerRepository
{
    /**
     * @var Customer[]
     */
    private $customers;

    public function __construct()
    {
        $this->customers = [];
    }

    /**
     * @inheritdoc
     */
    public function ofId(CustomerId $id): ?Customer
    {
        if (array_key_exists($id->id(), $this->customers)) {
            return $this->customers[$id->id()];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(Customer $customer): CustomerRepository
    {
        $this->customers[$customer->id()->id()] = $customer;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Customer $customer): CustomerRepository
    {
        $id = $customer->id()->id();
        if (!array_key_exists($id, $this->customers)) {
            throw new CustomerDoesNotExistException(sprintf(
                'Customer with id "%s" does not exist',
                $id
            ));
        }
        unset($this->customers[$id]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): CustomerId
    {
        return new CustomerId();
    }

    public function ofEmail(CustomerEmail $email): ?Customer
    {
        $customers = $this->customers;
        $filtered = array_values(
            array_filter($customers, function (Customer $customer) use ($email) {
                return $customer->email()->equals($email);
            })
        );
        if (sizeof($filtered) > 0) {
            return $filtered[0];
        }

        return null;
    }
}
