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
use ServerStatus\Domain\Model\AlertNotification\InvalidAlertNotificationStatusException;

final class AlertNotificationsSummaryByStatus implements \Countable
{
    /**
     * @var AlertNotificationStatus
     */
    private $status;

    /**
     * @var AlertNotificationsSummaryByStatusItem[]
     */
    private $items;

    /**
     * @internal
     * @var integer
     */
    private $count;


    public function __construct(AlertNotificationStatus $status, $items = [])
    {
        $this->status = $status;
        $this->items = $this->processItems($items);
    }

    /**
     * @param AlertNotificationsSummaryByStatusItem[]|AlertNotificationsSummaryByStatusItem $items
     * @return AlertNotificationsSummaryByStatusItem[]
     * @throws InvalidAlertNotificationStatusException
     */
    private function processItems($items = [])
    {
        if (!is_iterable($items)) {
            $items = [$items];
        }
        $processed = [];
        foreach ($items as $item) {
            if (!$item instanceof AlertNotificationsSummaryByStatusItem) {
                throw new \UnexpectedValueException(
                    sprintf('Only AlertNotificationsSummaryByStatusItem objects accepted')
                );
            }
            if (!$this->status()->equals($item->status())) {
                throw new InvalidAlertNotificationStatusException(
                    sprintf(
                        'Items should have status "%s", but item with status "%s" received',
                        $this->status()->code(),
                        $item->status()->code()
                    )
                );
            }
            $processed[] = $item;
        }

        return $processed;
    }

    public function status(): AlertNotificationStatus
    {
        return $this->status;
    }

    public function count()
    {
        if (is_null($this->count)) {
            $count = 0;
            foreach ($this->items as $item) {
                $count += $item->status()->equals($item->status()) ? $item->count() : 0;
            }
            $this->count = $count;
        }

        return $this->count;
    }
}
