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

namespace ServerStatus\Domain\Model\Customer;

interface CustomerRepository
{
    /**
     * @param CustomerId $id
     * @return null|Customer
     */
    public function ofId(CustomerId $id): ?Customer;

    /**
     * @param Customer $customer
     * @return CustomerRepository
     */
    public function add(Customer $customer): CustomerRepository;

    /**
     * @param Customer $customer
     * @return CustomerRepository
     * @throws CustomerDoesNotExistException
     */
    public function remove(Customer $customer): CustomerRepository;

    /**
     * @return CustomerId
     */
    public function nextId(): CustomerId;

    /**
     * @param CustomerEmail $email
     * @return null|Customer
     */
    public function ofEmail(CustomerEmail $email): ?Customer;
}
