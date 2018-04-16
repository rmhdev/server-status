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

namespace ServerStatus\Tests\Domain\Model\AlertNotification\Summary;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationStatusDataBuilder;

class AlertNotificationSummaryByStatusTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveEmptyListWhenNoNotificationsAreDefined()
    {
        $summary = AlertNotificationSummaryByStatusDataBuilder::anAlertNotificationSummaryByStatus()
            ->build();

        $this->assertEquals(0, $summary->count());
    }

    /**
     * @test
     */
    public function itShouldSumAllCountsInItems()
    {
        $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::SENT)
            ->build();
        $items = [
            AlertNotificationSummaryByStatusItemDataBuilder::anAlertNotificationSummaryByStatusItem()
                ->withStatus($status)->withCount(10)->build(),
            AlertNotificationSummaryByStatusItemDataBuilder::anAlertNotificationSummaryByStatusItem()
                ->withStatus($status)->withCount(5)->build()
        ];
        $summary = AlertNotificationSummaryByStatusDataBuilder::anAlertNotificationSummaryByStatus()
            ->withStatus($status)
            ->withItems($items)
            ->build();

        $this->assertEquals(15, $summary->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleItem()
    {
        $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::SENT)
            ->build();
        $item = AlertNotificationSummaryByStatusItemDataBuilder::anAlertNotificationSummaryByStatusItem()
            ->withStatus($status)
            ->withCount(10)
            ->build();
        $summary = AlertNotificationSummaryByStatusDataBuilder::anAlertNotificationSummaryByStatus()
            ->withStatus($status)
            ->withItems($item)
            ->build();

        $this->assertEquals(10, $summary->count());
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\AlertNotification\InvalidAlertNotificationStatusException
     */
    public function itShouldThrowExceptionWhenAddingItemsWithIncorrectStatus()
    {
        $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::SENT)
            ->build();
        $statusB = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::ERROR)
            ->build();
        $items = [
            AlertNotificationSummaryByStatusItemDataBuilder::anAlertNotificationSummaryByStatusItem()
                ->withStatus($statusB)
                ->withCount(5)
                ->build(),
        ];
        AlertNotificationSummaryByStatusDataBuilder::anAlertNotificationSummaryByStatus()
            ->withStatus($status)
            ->withItems($items)
            ->build();
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenUsingIncorrectType()
    {
        $status = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()->build();
        AlertNotificationSummaryByStatusDataBuilder::anAlertNotificationSummaryByStatus()
            ->withStatus($status)
            ->withItems(new \DateTimeImmutable("2018-04-16T12:00:00+0200"))
            ->build();
    }
}
