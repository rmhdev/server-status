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

namespace ServerStatus\Tests\Domain\Model\AlertNotification;

use ServerStatus\Domain\Model\AlertNotification\AlertNotificationCollection;

class AlertNotificationCollectionDataBuilder
{
    private $notifications;

    public function __construct()
    {
        $this->notifications = [];
    }

    public function withAlertNotifications($notifications = []): AlertNotificationCollectionDataBuilder
    {
        $this->notifications = $notifications;

        return $this;
    }

    public function build(): AlertNotificationCollection
    {
        return new AlertNotificationCollection($this->notifications);
    }

    public static function anAlertNotificationCollection(): AlertNotificationCollectionDataBuilder
    {
        return new self();
    }
}
