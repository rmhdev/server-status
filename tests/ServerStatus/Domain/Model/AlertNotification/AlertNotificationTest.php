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

use PHPUnit\Framework\TestCase;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;

class AlertNotificationTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnValueToString()
    {
        $id = AlertNotificationIdDataBuilder::anAlertNotificationId()->withValue("my-notification")->build();
        $alert = AlertDataBuilder::anAlert()->build();
        $notification = AlertNotificationDataBuilder::anAlertNotification()->withId($id)->withAlert($alert)->build();

        $this->assertSame("my-notification", (string) $notification);
    }

    /**
     * @test
     */
    public function itShouldBeComparableUsingTheDate()
    {
        $date = new \DateTimeImmutable("2018-01-28 23:00:00", new \DateTimeZone("Europe/Madrid"));
        $notification = AlertNotificationDataBuilder::anAlertNotification()->withDate($date)->build();

        $this->assertEquals(1, $notification->compareTo(
            AlertNotificationDataBuilder::anAlertNotification()->withDate($date->modify("-1 second"))->build()
        ));
        $this->assertEquals(0, $notification->compareTo(
            AlertNotificationDataBuilder::anAlertNotification()->withDate($date)->build()
        ));
        $this->assertEquals(-1, $notification->compareTo(
            AlertNotificationDataBuilder::anAlertNotification()->withDate($date->modify("+1 second"))->build()
        ));
    }
}
