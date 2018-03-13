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

use ServerStatus\Domain\Model\Alert\Alert;

interface AlertNotificationFactory
{
    public function build(
        AlertNotificationId $id,
        Alert $alert,
        \DateTimeInterface $dateTime,
        AlertNotificationStatus $status
    ): AlertNotification;
}
