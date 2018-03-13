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

namespace ServerStatus\Tests\Domain\Model\AlertNotification\Log;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationCollection;
use ServerStatus\Domain\Model\AlertNotification\Log\AlertNotificationsLog;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationCollectionDataBuilder;

class AlertNotificationsLogDataBuilder
{
    /**
     * @var Alert
     */
    private $alert;

    /**
     * @var DateRange
     */
    private $dateRange;

    /**
     * @var AlertNotificationCollection
     */
    private $alertNotificationCollection;


    public function __construct()
    {
        $this->alert = AlertDataBuilder::anAlert()->build();
        $this->dateRange = DateRangeFactory::create(
            DateRangeDay::NAME,
            new \DateTimeImmutable("2018-03-03T12:00:00+0200")
        );
        $this->alertNotificationCollection = AlertNotificationCollectionDataBuilder::anAlertNotificationCollection()
            ->build();
    }

    public function withAlert(Alert $alert): AlertNotificationsLogDataBuilder
    {
        $this->alert = $alert;

        return $this;
    }

    public function withDateRange(DateRange $dateRange): AlertNotificationsLogDataBuilder
    {
        $this->dateRange = $dateRange;

        return $this;
    }

    public function withCollection(?AlertNotificationCollection $collection): AlertNotificationsLogDataBuilder
    {
        $this->alertNotificationCollection = $collection;

        return $this;
    }

    public function build()
    {
        return new AlertNotificationsLog($this->alert, $this->dateRange, $this->alertNotificationCollection);
    }

    public static function anAlertNotificationsLog(): AlertNotificationsLogDataBuilder
    {
        return new self();
    }
}
