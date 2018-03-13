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

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationCollectionDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationDataBuilder;

class AlertNotificationsLogTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAnEmptyCollectionByDefault()
    {
        $log = AlertNotificationsLogDataBuilder::anAlertNotificationsLog()->withCollection(null)->build();

        $this->assertEquals(0, $log->alertNotificationCollection()->count());
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\AlertNotification\InvalidAlertNotificationException
     */
    public function itShouldThrowExceptionWhenAlertNotificationIsNotRelatedToAlert()
    {
        $collectionWithDifferentAlert = AlertNotificationCollectionDataBuilder::anAlertNotificationCollection()
            ->withAlertNotifications([
                AlertNotificationDataBuilder::anAlertNotification()
                    ->withDate(new \DateTimeImmutable("2018-03-03T11:00:00+0200"))
                    ->withAlert(AlertDataBuilder::anAlert()->build())
                    ->build()
            ])->build();

        $dateRange = DateRangeFactory::create(DateRangeDay::NAME, new \DateTimeImmutable("2018-03-03T12:00:00+0200"));
        $alert = AlertDataBuilder::anAlert()->build();
        AlertNotificationsLogDataBuilder::anAlertNotificationsLog()
            ->withDateRange($dateRange)
            ->withAlert($alert)
            ->withCollection($collectionWithDifferentAlert)
            ->build();
    }
}
