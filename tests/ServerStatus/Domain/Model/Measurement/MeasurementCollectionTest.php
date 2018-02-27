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

namespace ServerStatus\Tests\Domain\Model\Measurement;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Measurement\MeasurementCollection;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class MeasurementCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptAnEmptyListOfMeasurements()
    {
        $collection = $this->createCollection();

        $this->assertEquals(0, $collection->count());
    }

    private function createCollection($measurements = []): MeasurementCollection
    {
        return MeasurementCollectionDataBuilder::aMeasurementCollection()->withMeasurements($measurements)->build();
    }

    /**
     * @test
     */
    public function itShouldCountMeasurements()
    {
        $collection = $this->createCollection([
            MeasurementDataBuilder::aMeasurement()->build(),
            MeasurementDataBuilder::aMeasurement()->build(),
            MeasurementDataBuilder::aMeasurement()->build(),
        ]);

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleMeasurement()
    {
        $collection = $this->createCollection(MeasurementDataBuilder::aMeasurement()->build());

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectItemDataProvider
     */
    public function itShouldThrowExceptionIfSomethingDifferentToMeasurementIsUsed($item)
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
}
