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
use ServerStatus\Domain\Model\AlertNotification\Log\AlertNotificationsLogCollection;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class AlertNotificationsLogCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptAnEmptyListOfLogs()
    {
        $collection = $this->createCollection();

        $this->assertEquals(0, $collection->count());
    }

    private function createCollection($alerts = []): AlertNotificationsLogCollection
    {
        return AlertNotificationsLogCollectionDataBuilder::anAlertNotificationCollection()
            ->withAlertNotificationsLogs($alerts)
            ->build();
    }

    /**
     * @test
     */
    public function itShouldCountLogs()
    {
        $collection = $this->createCollection([
            AlertNotificationsLogDataBuilder::anAlertNotificationsLog()->build(),
            AlertNotificationsLogDataBuilder::anAlertNotificationsLog()->build(),
            AlertNotificationsLogDataBuilder::anAlertNotificationsLog()->build(),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleLog()
    {
        $collection = $this->createCollection(
            AlertNotificationsLogDataBuilder::anAlertNotificationsLog()->build()
        );

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentToLogIsUsed($item)
    {
        $this->createCollection($item);
    }

    public function incorrectItemDataProvider()
    {
        return [
            [CustomerDataBuilder::aCustomer()->build()],
            ["hello"],
        ];
    }

    /**
     * @test
     */
    public function itShouldBeIterable()
    {
        $collection = $this->createCollection();

        $this->assertTrue(is_iterable($collection));
    }

    /**
     * @test
     */
    public function itShouldBeImmutable()
    {
        $collection = $this->createCollection([]);
        $iterator = $collection->getIterator();
        if (method_exists($iterator, 'append')) {
            /* @var \ArrayIterator $iterator */
            $iterator->append(AlertNotificationsLogDataBuilder::anAlertNotificationsLog()->build());
            $this->assertEquals(0, $collection->count(), 'Appending new objects should not change the collection');
        } else {
            $this->assertTrue(true, 'Iterator has not an append method');
        }
    }
}
