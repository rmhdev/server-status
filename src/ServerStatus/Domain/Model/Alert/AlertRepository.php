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

namespace ServerStatus\Domain\Model\Alert;

use ServerStatus\Domain\Model\Customer\CustomerId;

interface AlertRepository
{
    /**
     * @param AlertId $id
     * @return null|Alert
     */
    public function ofId(AlertId $id): ?Alert;

    /**
     * @param Alert $alert
     * @return AlertRepository
     */
    public function add(Alert $alert): AlertRepository;

    /**
     * @param Alert $alert
     * @return AlertRepository
     * @throws AlertDoesNotExistException
     */
    public function remove(Alert $alert): AlertRepository;

    /**
     * @return AlertId
     */
    public function nextId(): AlertId;

    /**
     * @return AlertCollection
     */
    public function byCustomer(CustomerId $id): AlertCollection;
}
