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

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\AlertNotification\AlertNotification;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationId;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;

class AlertNotificationDataBuilder
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
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @var AlertNotificationStatus
     */
    private $status;


    public function __construct()
    {
        $this->id = AlertNotificationIdDataBuilder::anAlertNotificationId()->build();
        $this->alert = AlertDataBuilder::anAlert()->build();
        $this->date = new \DateTimeImmutable("2018-01-28T23:00:00+0200");
        $this->status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()->build();
    }

    public function withId(AlertNotificationId $id): AlertNotificationDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function withAlert(Alert $alert): AlertNotificationDataBuilder
    {
        $this->alert = $alert;

        return $this;
    }

    public function withDate(\DateTimeInterface $dateTime): AlertNotificationDataBuilder
    {
        $this->date = \DateTimeImmutable::createFromFormat(
            DATE_ISO8601,
            $dateTime->format(DATE_ISO8601),
            $dateTime->getTimezone()
        );

        return $this;
    }

    /**
     * @param string|AlertNotificationStatus $status
     * @return AlertNotificationDataBuilder
     */
    public function withStatus($status): AlertNotificationDataBuilder
    {
        if (is_string($status)) {
            $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()->withCode($status)->build();
        }
        $this->status = $status;

        return $this;
    }

    public function build(): AlertNotification
    {
        return new AlertNotification($this->id, $this->alert, $this->date, $this->status);
    }

    public static function anAlertNotification(): AlertNotificationDataBuilder
    {
        return new self();
    }
}
