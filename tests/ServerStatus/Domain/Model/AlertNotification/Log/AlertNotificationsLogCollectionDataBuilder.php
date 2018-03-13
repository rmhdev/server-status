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

namespace ServerStatus\Tests\Domain\Model\AlertNotification\Log;

use ServerStatus\Domain\Model\AlertNotification\Log\AlertNotificationsLogCollection;

class AlertNotificationsLogCollectionDataBuilder
{
    private $logs;

    public function __construct()
    {
        $this->logs = [];
    }

    public function withAlertNotificationsLogs($logs = []): AlertNotificationsLogCollectionDataBuilder
    {
        $this->logs = $logs;

        return $this;
    }

    public function build(): AlertNotificationsLogCollection
    {
        return new AlertNotificationsLogCollection($this->logs);
    }

    public static function anAlertNotificationCollection(): AlertNotificationsLogCollectionDataBuilder
    {
        return new self();
    }
}
