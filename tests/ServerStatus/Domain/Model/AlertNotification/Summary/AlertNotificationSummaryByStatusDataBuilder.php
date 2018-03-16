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

use ServerStatus\Domain\Model\AlertNotification\Summary\AlertNotificationsSummaryByStatus;
use ServerStatus\Domain\Model\AlertNotification\Summary\AlertNotificationsSummaryByStatusItem;

final class AlertNotificationSummaryByStatusDataBuilder
{
    /**
     * @var AlertNotificationsSummaryByStatusItem[]
     */
    private $items;


    public function __construct()
    {
        $this->items = [];
    }

    public function withItems($items): AlertNotificationSummaryByStatusDataBuilder
    {
        $this->items = $items;

        return $this;
    }

    public function build(): AlertNotificationsSummaryByStatus
    {
        return new AlertNotificationsSummaryByStatus($this->items);
    }

    public static function anAlertNotificationSummaryByStatus(): AlertNotificationSummaryByStatusDataBuilder
    {
        return new self();
    }
}
