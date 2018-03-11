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

namespace ServerStatus\Infrastructure\Persistence\InMemory\Alert;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Alert\AlertCollection;
use ServerStatus\Domain\Model\Alert\AlertDoesNotExistException;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\Customer\CustomerId;

class InMemoryAlertRepository implements AlertRepository
{
    /**
     * @var Alert[]
     */
    private $alerts;


    public function __construct()
    {
        $this->alerts = [];
    }

    /**
     * @inheritdoc
     */
    public function ofId(AlertId $id): ?Alert
    {
        $key = $id->id();
        if (array_key_exists($key, $this->alerts)) {
            return $this->alerts[$key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(Alert $alert): AlertRepository
    {
        $key = $alert->id()->id();
        $this->alerts[$key] = $alert;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(Alert $alert): AlertRepository
    {
        $id = $alert->id()->id();
        if (!array_key_exists($id, $this->alerts)) {
            throw new AlertDoesNotExistException(
                sprintf('Alert with id "%s" does not exist', $id)
            );
        }
        unset($this->alerts[$id]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): AlertId
    {
        return new AlertId();
    }

    /**
     * @inheritdoc
     */
    public function byCustomer(CustomerId $id): AlertCollection
    {
        $alerts = $this->alerts;

        return new AlertCollection(array_filter($alerts, function (Alert $alert) use ($id) {
            return $alert->customer()->id()->equals($id);
        }));
    }
}
