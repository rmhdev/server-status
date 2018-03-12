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

final class AlertNotificationCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var AlertNotification[]
     */
    private $notifications;


    public function __construct($notifications = [])
    {
        $this->notifications = $this->processNotifications($notifications);
    }

    private function processNotifications($notifications = [])
    {
        $notifications = is_iterable($notifications) ? $notifications : [$notifications];
        $processed = [];
        foreach ($notifications as $notification) {
            $this->assertNotification($notification);
            $processed[] = $notification;
        }

        return $processed;
    }

    private function assertNotification($notification)
    {
        if (!is_object($notification) || !$notification instanceof AlertNotification) {
            throw new \UnexpectedValueException(sprintf(
                'AlertNotificationCollection only accepts "AlertNotification" objects, "%s" received',
                gettype($notification)
            ));
        }
    }

    private function notifications(): array
    {
        return $this->notifications;
    }

    public function count(): int
    {
        return sizeof($this->notifications);
    }

    /**
     * @return \Iterator|AlertNotification[]
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->notifications());
    }
}
