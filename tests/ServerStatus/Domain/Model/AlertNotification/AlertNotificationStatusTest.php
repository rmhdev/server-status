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
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;

class AlertNotificationStatusTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBeReadyByDefault()
    {
        $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()->withCode("")->build();

        $this->assertSame($status::READY, $status->code());
    }

    /**
     * @test
     */
    public function itShouldBeAcceptCorrectCode()
    {
        $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::SENT)
            ->build();

        $this->assertSame($status::SENT, $status->code());
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\AlertNotification\InvalidAlertNotificationStatusException
     */
    public function itShouldThrowExceptionWhenIncorrectCodeIsUsed()
    {
        AlertNotificationStatusDataBuilder::anAlertNotificationStatus()->withCode("lorem")->build();
    }

    /**
     * @test
     */
    public function itShouldReturnTrueWhenComparedWithSameCode()
    {
        $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::SENT)
            ->build();

        $this->assertTrue($status->isCode($status::SENT));
        $this->assertFalse($status->isCode($status::ERROR));
    }
}
