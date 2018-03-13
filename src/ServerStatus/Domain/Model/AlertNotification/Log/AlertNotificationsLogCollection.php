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

final class AlertNotificationsLogCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var AlertNotificationsLog[]
     */
    private $logs;


    public function __construct($logs = [])
    {
        $this->logs = $this->processAlertNotificationsLogs($logs);
    }

    private function processAlertNotificationsLogs($logs = [])
    {
        $logs = is_iterable($logs) ? $logs : [$logs];
        $processed = [];
        foreach ($logs as $notification) {
            $this->assertAlertNotificationsLog($notification);
            $processed[] = $notification;
        }
        //usort($processed, function (AlertNotificationsLog $a, AlertNotificationsLog $b) {
        //    return $a->compareTo($b);
        //});

        return $processed;
    }

    private function assertAlertNotificationsLog($log)
    {
        if (!is_object($log) || !$log instanceof AlertNotificationsLog) {
            throw new \UnexpectedValueException(sprintf(
                'AlertNotificationsLogCollection only accepts "AlertNotificationsLog" objects, "%s" received',
                gettype($log)
            ));
        }
    }

    private function logs(): array
    {
        return $this->logs;
    }

    public function count(): int
    {
        return sizeof($this->logs);
    }

    /**
     * @return \Iterator|AlertNotificationsLog[]
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->logs());
    }
}
