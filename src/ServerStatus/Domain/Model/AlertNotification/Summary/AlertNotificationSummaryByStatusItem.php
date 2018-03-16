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

namespace ServerStatus\Domain\Model\AlertNotification\Summary;

use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;

final class AlertNotificationsSummaryByStatusItem
{
    /**
     * @var AlertNotificationStatus
     */
    private $status;

    /**
     * @var int
     */
    private $count;


    public function __construct(AlertNotificationStatus $status, int $count = 0)
    {
        $this->assertCount($count);
        $this->status = $status;
        $this->count = $count;
    }

    private function assertCount(int $count)
    {
        if (0 > $count) {
            throw new \UnexpectedValueException(sprintf('Count should not be negative, "%s" received', $count));
        }
    }

    public function status(): AlertNotificationStatus
    {
        return $this->status;
    }

    public function count(): int
    {
        return $this->count;
    }
}
