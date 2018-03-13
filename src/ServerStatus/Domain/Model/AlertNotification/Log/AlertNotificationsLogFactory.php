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

namespace ServerStatus\Domain\Model\AlertNotification\Log;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;

final class AlertNotificationsLogFactory
{
    public static function create(
        Alert $alert,
        DateRange $dateRange,
        AlertNotificationRepository $repository
    ): AlertNotificationsLog {

        return new AlertNotificationsLog(
            $alert,
            $dateRange,
            $repository->byAlert($alert->id(), $dateRange)
        );
    }
}
