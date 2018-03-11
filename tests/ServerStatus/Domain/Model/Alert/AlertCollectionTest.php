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

namespace ServerStatus\Tests\Domain\Model\Alert;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Alert\AlertCollection;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class AlertCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptAnEmptyListOfAlerts()
    {
        $collection = $this->createCollection();

        $this->assertEquals(0, $collection->count());
    }

    private function createCollection($alerts = []): AlertCollection
    {
        return AlertCollectionDataBuilder::anAlertCollection()->withAlerts($alerts)->build();
    }

    /**
     * @test
     */
    public function itShouldCountAlerts()
    {
        $collection = $this->createCollection([
            AlertDataBuilder::anAlert()->build(),
            AlertDataBuilder::anAlert()->build(),
            AlertDataBuilder::anAlert()->build(),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleAlert()
    {
        $collection = $this->createCollection(AlertDataBuilder::anAlert()->build());

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentToAlertIsUsed($item)
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
            $iterator->append(AlertDataBuilder::anAlert()->build());
            $this->assertEquals(0, $collection->count(), 'Appending new objects should not change the collection');
        } else {
            $this->assertTrue(true, 'Iterator has not an append method');
        }
    }
}
