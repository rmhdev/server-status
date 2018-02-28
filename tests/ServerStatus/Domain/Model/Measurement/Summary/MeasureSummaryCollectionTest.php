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

namespace ServerStatus\Tests\Domain\Model\Measurement\Summary;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryCollection;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryFactory;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class MeasureSummaryCollectionTest extends TestCase
{
    private $measurementRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->measurementRepository = new InMemoryMeasurementRepository();
    }

    protected function tearDown()
    {
        unset($this->measurementRepository);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function itShouldAcceptAnEmptyList()
    {
        $collection = $this->createCollection([]);

        $this->assertEquals(0, $collection->count());
    }

    private function createCollection($values = []): MeasureSummaryCollection
    {
        //return MeasureSummaryCollectionDataBuilder::aMeasureSummaryCollection()->withChecks($values)->build();
        return new MeasureSummaryCollection($values);
    }

    /**
     * @test
     */
    public function itShouldCountSummaries()
    {
        $collection = $this->createCollection([
            $this->createMeasureSummary(),
            $this->createMeasureSummary(),
            $this->createMeasureSummary(),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    private function createMeasureSummary(Check $check = null)
    {
        return MeasureSummaryFactory::create(
            $check ? $check : CheckDataBuilder::aCheck()->build(),
            $this->measurementRepository,
            DateRangeFactory::create(DateRangeDay::NAME, new \DateTimeImmutable("2018-02-02T15:24:10+0200"))
        );
    }

    public function itShouldAcceptIterableList()
    {
        $collection = $this->createCollection(
            new \ArrayIterator([
                $this->createMeasureSummary(),
                $this->createMeasureSummary(),
                $this->createMeasureSummary(),
            ])
        );

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleItem()
    {
        $collection = $this->createCollection($this->createMeasureSummary());

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldBeIterable()
    {
        $collection = $this->createCollection($this->createMeasureSummary());

        $this->assertInstanceOf(\Iterator::class, $collection->getIterator());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentToMeasurementSummaryIsUsed($item)
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
    public function itShouldBeImmutable()
    {
        $collection = $this->createCollection([]);

        $iterator = $collection->getIterator();
        if (method_exists($iterator, 'append')) {
            /* @var \ArrayIterator $iterator */
            $iterator->append(
                $this->createMeasureSummary()
            );
            $this->assertEquals(0, $collection->count(), 'Appending new objects should not change the collection');
        } else {
            $this->assertTrue(true, 'Iterator has not an append method');
        }
    }

    /**
     * @test
     */
    public function isShouldFilterByCustomerId()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($id)->build();

        $summary1 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->withCustomer($customer)->build());
        $summary2 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->withCustomer($customer)->build());
        $summary3 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->build());

        $collection = $this->createCollection([$summary1, $summary2, $summary3]);
        $expectedCollection = $this->createCollection([$summary1, $summary2]);

        $this->assertEquals($expectedCollection, $collection->byCustomerId($id));
    }

    /**
     * @test
     */
    public function isShouldFilterByCheckId()
    {
        $checkId = CheckIdDataBuilder::aCheckId()->build();

        $summary1 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->build());
        $summary2 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->withId($checkId)->build());
        $summary3 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->withId($checkId)->build());

        $collection = $this->createCollection([$summary1, $summary2, $summary3]);
        $expectedCollection = $this->createCollection([$summary2, $summary3]);

        $this->assertEquals($expectedCollection, $collection->byCheckId($checkId));
    }
}
