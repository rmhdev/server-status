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
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationCollection;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class AlertNotificationCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptAnEmptyListOfNotifications()
    {
        $collection = $this->createCollection();

        $this->assertEquals(0, $collection->count());
    }

    private function createCollection($alerts = []): AlertNotificationCollection
    {
        return AlertNotificationCollectionDataBuilder::anAlertNotificationCollection()
            ->withAlertNotifications($alerts)
            ->build();
    }

    /**
     * @test
     */
    public function itShouldCountAlerts()
    {
        $collection = $this->createCollection([
            AlertNotificationDataBuilder::anAlertNotification()->build(),
            AlertNotificationDataBuilder::anAlertNotification()->build(),
            AlertNotificationDataBuilder::anAlertNotification()->build(),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleNotification()
    {
        $collection = $this->createCollection(AlertNotificationDataBuilder::anAlertNotification()->build());

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentToAlertNotificationIsUsed($item)
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
            $iterator->append(AlertNotificationDataBuilder::anAlertNotification()->build());
            $this->assertEquals(0, $collection->count(), 'Appending new objects should not change the collection');
        } else {
            $this->assertTrue(true, 'Iterator has not an append method');
        }
    }

    /**
     * @test
     */
    public function itShouldReturnTheItemsOrderedByDate()
    {
        $notifications = [
            AlertNotificationDataBuilder::anAlertNotification()
                ->withDate(new \DateTimeImmutable("2018-03-03T12:00:00+0200"))
                ->withId("second")
                ->build(),
            AlertNotificationDataBuilder::anAlertNotification()
                ->withDate(new \DateTimeImmutable("2018-03-04T11:59:59+0200"))
                ->withId("third")
                ->build(),
            AlertNotificationDataBuilder::anAlertNotification()
                ->withDate(new \DateTimeImmutable("2018-03-03T11:59:59+0200"))
                ->withId("first")
                ->build(),
        ];
        $collection = $this->createCollection($notifications);
        $iterator = $collection->getIterator();

        $this->assertSame("first", $iterator->current()->id()->id());
        $iterator->next();
        $this->assertSame("second", $iterator->current()->id()->id());
        $iterator->next();
        $this->assertSame("third", $iterator->current()->id()->id());
    }
}
