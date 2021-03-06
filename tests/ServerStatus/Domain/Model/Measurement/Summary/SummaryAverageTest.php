<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\tests\Domain\Model\Measurement\Summary;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeCustom;
use ServerStatus\Domain\Model\Measurement\Summary\SummaryAverage;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDurationDataBuilder;

class SummaryAverageTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnZeroResponseTimeWhenNoDataIsDefined()
    {
        $summaryAverage = $this->createSummaryAverageWithValues([]);

        $this->assertEquals(
            MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(0)->build(),
            $summaryAverage->responseTime()
        );
    }

    private function createSummaryAverageWithValues($values = []): SummaryAverage
    {
        return new SummaryAverage(
            new DateRangeCustom(
                new \DateTimeImmutable("2018-02-03T15:20:00+0200"),
                new \DateTimeImmutable("2018-02-03T15:30:00+0200")
            ),
            $values
        );
    }

    /**
     * @test
     */
    public function itShouldAverageResponseTimeForGivenValues()
    {
        $values = [
            ["response_time" => 1.000100],
            ["response_time" => 1.000200],
            ["response_time" => 1.000300],
            ["response_time" => 1.000400],
        ];
        $summaryAverage = $this->createSummaryAverageWithValues($values);

        $this->assertEquals(
            MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1.000250)->build(),
            $summaryAverage->responseTime()
        );
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleValueWithData()
    {
        $summaryAverage = $this->createSummaryAverageWithValues(["response_time" => 1.000100]);

        $this->assertEquals(
            MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1.000100)->build(),
            $summaryAverage->responseTime()
        );
    }
}
