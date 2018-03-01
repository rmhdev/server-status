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

namespace ServerStatus\Tests\Domain\Model\Measurement\Performance;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatusCollection;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDurationDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementStatusDataBuilder;

class PerformanceStatusCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptEmptyList()
    {
        $this->assertEquals(0, $this->createCollection([])->count());
    }

    private function createCollection($values)
    {
        return PerformanceStatusCollectionDataBuilder::aPerformanceStatusCollection()
            ->withValues($values)->build();
    }

    /**
     * @test
     */
    public function itShouldCountMeasurements()
    {
        $collection = $this->createCollection([
            PerformanceStatusDataBuilder::aPerformanceStatus()->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->build(),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleItem()
    {
        $collection = $this->createCollection(PerformanceStatusDataBuilder::aPerformanceStatus()->build());

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentToPerformanceStatusIsUsed($item)
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
        $collection = $this->createCollection([]);

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
                PerformanceStatusDataBuilder::aPerformanceStatus()->build()
            );
            $this->assertEquals(0, $collection->count(), 'Appending new objects should not change the collection');
        } else {
            $this->assertTrue(true, 'Iterator has not an append method');
        }
    }

    /**
     * @test
     */
    public function itShouldCalculateTheTotalAverageResponseTime()
    {
        $expected = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(
            (100 + 200 + 250 + 300 + 400 + 1000) / 6
        )->build();

        $this->assertEquals($expected, $this->createCollectionWithData()->averageDuration());
    }

    private function createCollectionWithData(): PerformanceStatusCollection
    {
        return $this->createCollection([
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(100)->build()
            )->withDurationAverage(
                MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(100)->build()
            )->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(200)->build()
            )->withDurationAverage(
                MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(200)->build()
            )->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(200)->build()
            )->withDurationAverage(
                MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(250)->build()
            )->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(300)->build()
            )->withDurationAverage(
                MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(300)->build()
            )->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(404)->build()
            )->withDurationAverage(
                MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(400)->build()
            )->build(),
            PerformanceStatusDataBuilder::aPerformanceStatus()->withStatus(
                MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(500)->build()
            )->withDurationAverage(
                MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1000)->build()
            )->build(),
        ]);
    }

    /**
     * @test
     */
    public function itShouldCalculateTheAverageResponseTimeByStatus()
    {
        $expected = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(
            (200 + 250) / 2
        )->build();

        $this->assertEquals($expected, $this->createCollectionWithData()->averageDuration(
            MeasurementStatusDataBuilder::aMeasurementStatus()->withCode(200)->build()
        ));
    }
}
