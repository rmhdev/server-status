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

use ServerStatus\Domain\Model\AlertNotification\AlertNotificationId;

class AlertNotificationIdDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "";
    }

    public function withValue(string $value): AlertNotificationIdDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): AlertNotificationId
    {
        return new AlertNotificationId($this->value);
    }

    public static function anAlertNotificationId(): AlertNotificationIdDataBuilder
    {
        return new self();
    }
}
