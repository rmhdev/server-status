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
    private $dateTime;

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
        $this->dateTime = $this->createDateTime($dateTime);
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
        return $this->dateTime;
    }

    public function status(): AlertNotificationStatus
    {
        return $this->status;
    }

    public function __toString(): string
    {
        return sprintf('%s', $this->id());
    }
}
