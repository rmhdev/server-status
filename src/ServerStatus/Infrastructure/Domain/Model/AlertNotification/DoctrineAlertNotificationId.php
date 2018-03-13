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

use ServerStatus\Domain\Model\AlertNotification\AlertNotification;
use ServerStatus\Infrastructure\Domain\Model\DoctrineEntityId;

class DoctrineAlertNotificationId extends DoctrineEntityId
{
    public function getName()
    {
        return "AlertNotificationId";
    }

    public function getNamespace()
    {
        return substr(AlertNotification::class, 0, -strlen("\AlertNotification"));
    }
}
