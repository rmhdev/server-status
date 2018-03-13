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

namespace ServerStatus\Infrastructure\Domain\Model\AlertNotification;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\AlertNotification\AlertNotification;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationFactory;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationId;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;

class DoctrineAlertNotificationFactory implements AlertNotificationFactory
{
    public function build(
        AlertNotificationId $id,
        Alert $alert,
        \DateTimeInterface $dateTime,
        AlertNotificationStatus $status
    ): AlertNotification {
        return new AlertNotification($id, $alert, $dateTime, $status);
    }
}
