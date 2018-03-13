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

namespace ServerStatus\Domain\Model\AlertNotification;

use ServerStatus\Domain\Model\Alert\Alert;

class AlertNotification
{
    /**
     * @var AlertNotificationId
     */
    private $id;

    /**
     * @var Alert
     */
    private $alert;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var AlertNotificationStatus
     */
    private $status;


    public function __construct(
        AlertNotificationId $id,
        Alert $alert,
        \DateTimeInterface $dateTime,
        AlertNotificationStatus $status
    ) {
        $this->id = $id;
        $this->alert = $alert;
        $this->createdAt = $this->createDateTime($dateTime);
        $this->status = $status;
    }

    private function createDateTime(\DateTimeInterface $dateTime)
    {
        return \DateTime::createFromFormat(
            DATE_ISO8601,
            $dateTime->format(DATE_ISO8601),
            $dateTime->getTimezone()
        );
    }

    public function id(): AlertNotificationId
    {
        return $this->id;
    }

    public function alert(): Alert
    {
        return $this->alert;
    }

    public function dateTime(): \DateTime
    {
        return $this->createdAt;
    }

    public function status(): AlertNotificationStatus
    {
        return $this->status;
    }

    /**
     * @return int &lt; 0 if $this is less than
     * $notification; &gt; 0 if $this
     * is greater than $notification, and 0 if they are
     * equal.
     */
    public function compareTo(AlertNotification $notification): int
    {
        if ($notification->dateTime() > $this->dateTime()) {
            return -1;
        }
        if ($notification->dateTime() < $this->dateTime()) {
            return 1;
        }

        return 0;
    }

    public function __toString(): string
    {
        return sprintf('%s', $this->id());
    }
}
