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

namespace ServerStatus\Tests\Domain\Model\AlertNotification\Summary;

use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;
use ServerStatus\Domain\Model\AlertNotification\Summary\AlertNotificationsSummaryByStatusItem;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationStatusDataBuilder;

final class AlertNotificationSummaryByStatusItemDataBuilder
{
    /**
     * @var AlertNotificationStatus
     */
    private $status;

    /**
     * @var int
     */
    private $count;


    public function __construct()
    {
        $this->status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::READY)
            ->build();
        $this->count = 0;
    }

    public function withStatus($status): AlertNotificationSummaryByStatusItemDataBuilder
    {
        if (is_string($status)) {
            $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()->withCode($status)->build();
        }
        $this->status = $status;

        return $this;
    }

    public function withCount(int $count): AlertNotificationSummaryByStatusItemDataBuilder
    {
        $this->count = $count;

        return $this;
    }

    public function build(): AlertNotificationsSummaryByStatusItem
    {
        return new AlertNotificationsSummaryByStatusItem($this->status, $this->count);
    }

    public static function anAlertNotificationSummaryByStatusItem(): AlertNotificationSummaryByStatusItemDataBuilder
    {
        return new self();
    }
}
