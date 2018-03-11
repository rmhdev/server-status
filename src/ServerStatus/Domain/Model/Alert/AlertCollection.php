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

namespace ServerStatus\Domain\Model\Alert;

final class AlertCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var Alert[]
     */
    private $alerts;


    public function __construct($alerts = [])
    {
        $this->alerts = $this->processAlerts($alerts);
    }

    private function processAlerts($alerts = [])
    {
        $alerts = is_iterable($alerts) ? $alerts : [$alerts];
        $processed = [];
        foreach ($alerts as $check) {
            $this->assertAlert($check);
            $processed[] = $check;
        }

        return $processed;
    }

    private function assertAlert($alert)
    {
        if (!is_object($alert) || !$alert instanceof Alert) {
            throw new \UnexpectedValueException(sprintf(
                'AlertCollection only accepts "Alert" objects, "%s" received',
                gettype($alert)
            ));
        }
    }

    private function alerts(): array
    {
        return $this->alerts;
    }

    public function count(): int
    {
        return sizeof($this->alerts);
    }

    /**
     * @return \Iterator|Alert[]
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->alerts());
    }
}
