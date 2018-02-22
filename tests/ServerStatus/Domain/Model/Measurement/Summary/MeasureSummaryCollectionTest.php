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

namespace ServerStatus\Domain\Model\Measurement\Summary;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserIdDataBuilder;

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
            MeasureDaySummary::NAME,
            $check ? $check : CheckDataBuilder::aCheck()->build(),
            $this->measurementRepository,
            new \DateTimeImmutable("2018-02-02T15:24:10+0200")
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
            [UserDataBuilder::aUser()->build()],
            ["hello"],
        ];
    }

    /**
     * @test
     */
    public function isShouldFilterByUserId()
    {
        $userId = UserIdDataBuilder::aUserId()->build();
        $user = UserDataBuilder::aUser()->withId($userId)->build();

        $summary1 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->withUser($user)->build());
        $summary2 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->withUser($user)->build());
        $summary3 = $this->createMeasureSummary(CheckDataBuilder::aCheck()->build());

        $collection = $this->createCollection([$summary1, $summary2, $summary3]);
        $expectedCollection = $this->createCollection([$summary1, $summary2]);

        $this->assertEquals($expectedCollection, $collection->byUserId($userId));
    }
}
