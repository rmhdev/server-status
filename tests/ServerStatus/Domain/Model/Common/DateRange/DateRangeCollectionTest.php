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

namespace ServerStatus\Tests\Domain\Model\Common\DateRange;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeCollection;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeWeek;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class DateRangeCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptAnEmptyList()
    {
        $collection = $this->createCollection();

        $this->assertEquals(0, $collection->count());
    }

    private function createCollection($dateRanges = []): DateRangeCollection
    {
        return DateRangeCollectionDataBuilder::aDateRangeCollection()->withDateRanges($dateRanges)->build();
    }

    /**
     * @test
     */
    public function itShouldCount()
    {
        $collection = $this->createCollection([
            DateRangeFactory::create(DateRangeDay::NAME, new \DateTime("2018-02-19T12:00:00+0200")),
            DateRangeFactory::create(DateRangeDay::NAME, new \DateTime("2018-02-20T12:00:00+0200")),
            DateRangeFactory::create(DateRangeDay::NAME, new \DateTime("2018-02-21T12:00:00+0200")),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleDateRange()
    {
        $collection = $this->createCollection(
            DateRangeFactory::create(DateRangeWeek::NAME, new \DateTime("2018-02-19T12:00:00+0200"))
        );

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentIsUsed($item)
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
            $iterator->append(
                DateRangeFactory::create(DateRangeDay::NAME, new \DateTime("2018-02-19T12:00:00+0200"))
            );
            $this->assertEquals(0, $collection->count(), 'Appending new objects should not change the collection');
        } else {
            $this->assertTrue(true, 'Iterator has not an append method');
        }
    }
}
