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
}
