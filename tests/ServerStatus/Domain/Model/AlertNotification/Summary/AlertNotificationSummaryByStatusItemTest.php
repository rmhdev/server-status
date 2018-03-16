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

class AlertNotificationSummaryByStatusItemTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveZeroAsCountValue()
    {
        $summary = AlertNotificationSummaryByStatusItemDataBuilder::anAlertNotificationSummaryByStatusItem()
            ->build();

        $this->assertEquals(0, $summary->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenCountHasNegativeValue()
    {
        AlertNotificationSummaryByStatusItemDataBuilder::anAlertNotificationSummaryByStatusItem()
            ->withCount(-1)
            ->build();
    }
}
