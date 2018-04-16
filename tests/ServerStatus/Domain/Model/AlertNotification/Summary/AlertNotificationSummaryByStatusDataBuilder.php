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
use ServerStatus\Domain\Model\AlertNotification\Summary\AlertNotificationsSummaryByStatus;
use ServerStatus\Domain\Model\AlertNotification\Summary\AlertNotificationsSummaryByStatusItem;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationStatusDataBuilder;

final class AlertNotificationSummaryByStatusDataBuilder
{
    private $status;

    /**
     * @var AlertNotificationsSummaryByStatusItem[]
     */
    private $items;


    public function __construct()
    {
        $this->status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::SENT)
            ->build();
        $this->items = [];
    }

    public function withStatus(AlertNotificationStatus $status): AlertNotificationSummaryByStatusDataBuilder
    {
        $this->status = $status;

        return $this;
    }

    public function withItems($items): AlertNotificationSummaryByStatusDataBuilder
    {
        $this->items = $items;

        return $this;
    }

    public function build(): AlertNotificationsSummaryByStatus
    {
        return new AlertNotificationsSummaryByStatus($this->status, $this->items);
    }

    public static function anAlertNotificationSummaryByStatus(): AlertNotificationSummaryByStatusDataBuilder
    {
        return new self();
    }
}
