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
}
