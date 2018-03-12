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

namespace ServerStatus\Domain\Model\AlertNotification;

interface AlertNotificationRepository
{
    /**
     * @param AlertNotificationId $id
     * @return null|AlertNotification
     */
    public function ofId(AlertNotificationId $id): ?AlertNotification;

    /**
     * @param AlertNotification $notification
     * @return AlertNotificationRepository
     */
    public function add(AlertNotification $notification): AlertNotificationRepository;

    /**
     * @param AlertNotification $notification
     * @return AlertNotificationRepository
     * @throws AlertNotificationDoesNotExistException
     */
    public function remove(AlertNotification $notification): AlertNotificationRepository;

    /**
     * @return AlertNotificationId
     */
    public function nextId(): AlertNotificationId;
}
