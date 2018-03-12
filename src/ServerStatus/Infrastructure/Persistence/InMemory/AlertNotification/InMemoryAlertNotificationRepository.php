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

namespace ServerStatus\Infrastructure\Persistence\InMemory\AlertNotification;

use ServerStatus\Domain\Model\AlertNotification\AlertNotification;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationDoesNotExistException;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationId;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;

class InMemoryAlertNotificationRepository implements AlertNotificationRepository
{
    /**
     * @var AlertNotification[]
     */
    private $alertNotifications;


    public function __construct()
    {
        $this->alertNotifications = [];
    }

    /**
     * @inheritdoc
     */
    public function ofId(AlertNotificationId $id): ?AlertNotification
    {
        $key = $id->id();
        if (array_key_exists($key, $this->alertNotifications)) {
            return $this->alertNotifications[$key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(AlertNotification $notification): AlertNotificationRepository
    {
        $key = $notification->id()->id();
        $this->alertNotifications[$key] = $notification;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(AlertNotification $notification): AlertNotificationRepository
    {
        $id = $notification->id()->id();
        if (!array_key_exists($id, $this->alertNotifications)) {
            throw new AlertNotificationDoesNotExistException(
                sprintf('Alert notification "%s" cannot be removed from "in memory" repository', $id)
            );
        }
        unset($this->alertNotifications[$id]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): AlertNotificationId
    {
        return new AlertNotificationId();
    }
}
