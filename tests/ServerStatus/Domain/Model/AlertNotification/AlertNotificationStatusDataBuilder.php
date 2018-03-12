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

use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;

class AlertNotificationStatusDataBuilder
{
    private $code;

    public function __construct()
    {
        $this->code = "";
    }

    public function withCode(string $code): AlertNotificationStatusDataBuilder
    {
        $this->code = $code;

        return $this;
    }

    public function build(): AlertNotificationStatus
    {
        return new AlertNotificationStatus($this->code);
    }

    public static function anAlertNotificationStatus(): AlertNotificationStatusDataBuilder
    {
        return new self();
    }
}
