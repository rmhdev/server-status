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

namespace ServerStatus\Application\DataTransformer\Alert;

use ServerStatus\Domain\Model\AlertNotification\AlertNotificationCollection;
use ServerStatus\Domain\Model\AlertNotification\Log\AlertNotificationsLogCollection;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Customer\Customer;

final class CustomerAlertsDtoDataTransformer implements CustomerAlertsDataTransformer
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var DateRange
     */
    private $dateRange;

    /**
     * @var AlertNotificationsLogCollection
     */
    private $alertNotificationsLogCollection;


    public function write(
        Customer $customer,
        DateRange $dateRange,
        AlertNotificationsLogCollection $logCollection
    ) {
        $this->customer = $customer;
        $this->dateRange = $dateRange;
        $this->alertNotificationsLogCollection = $logCollection;
    }

    public function read()
    {
        return [
            "customer" => $this->readCustomer(),
            "date_range" => $this->readDateRange(),
            "alerts" => $this->readAlertNotificationsLogCollection(),
        ];
    }

    private function readCustomer()
    {
        return [
            "id" => $this->customer->id()->id(),
            "email" => $this->customer->email()->value(),
            "screen_name" => $this->customer->screenName(),
            "alias" => $this->customer->alias()->value(),
        ];
    }

    private function readDateRange()
    {
        return [
            "from" => $this->dateRange->from()->format(DATE_ISO8601),
            "to" => $this->dateRange->to()->format(DATE_ISO8601),
            "name" => $this->dateRange->name(),
            "formatted" => $this->dateRange->formatted(),
        ];
    }

    private function readAlertNotificationsLogCollection()
    {
        $alerts = [];
        foreach ($this->alertNotificationsLogCollection as $alertNotificationsLog) {
            $alerts[] = [
                "id" => $alertNotificationsLog->alert()->id()->id(),
                "is_enabled" => $alertNotificationsLog->alert()->isEnabled() ? "1" : "0",
                "check" => $this->readCheck($alertNotificationsLog->alert()->check()),
                "channel" => [
                    "name" => $alertNotificationsLog->alert()->channel()->name(),
                    "destination_raw" => $alertNotificationsLog->alert()->channel()->destinationRaw(),
                ],
                "reason" => [
                    "name" => $alertNotificationsLog->alert()->reason()->name(),
                ],
                "time_window" => [
                    "minutes" => $alertNotificationsLog->alert()->timeWindow()->minutes(),
                ],
                "notifications" => $this->readAlertNotificationCollection(
                    $alertNotificationsLog->alertNotificationCollection()
                )
            ];
        }

        return $alerts;
    }

    private function readCheck(?Check $check)
    {
        if (!$check) {
            return [];
        }

        return [
            "id" => $check->id()->id(),
            "name" => $check->name()->value(),
            "slug" => $check->name()->slug(),
            "url" => [
                "formatted" => $check->url()
            ]
        ];
    }

    private function readAlertNotificationCollection(AlertNotificationCollection $collection)
    {
        $notifications = [];
        foreach ($collection->getIterator() as $alertNotification) {
            $notifications[] = [
                "date" => $alertNotification->dateTime()->format(DATE_ISO8601),
                "status" => $alertNotification->status()->code(),
            ];
        }

        return $notifications;
    }
}
