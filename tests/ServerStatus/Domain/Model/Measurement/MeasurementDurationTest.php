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
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentDataBuilder;

class MeasurementDurationTest extends TestCase
{
    /**
     * @test
     * @dataProvider correctDurations
     */
    public function itShouldFormatAllTypeOfDuration($milliseconds, $expected)
    {
        $duration = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration($milliseconds)->build();

        $this->assertSame(
            $expected,
            $duration->formatted(),
            "Value {$milliseconds} should be formatted as {$expected}"
        );
    }

    public function correctDurations()
    {
        return [
            [150, "150.00 ms"],
            [150.12, "150.12 ms"],
            [1000, "1 second"],
            [1500, "1.5 seconds"],
            [60000, "1 minute"],
            [120000, "2 minutes"],
            [121000, "2m 01s"],
            [3600000, "1 hour"],
            [7200000, "2 hours"],
            [7261000, "2h 01m 01s"],
        ];
    }

    /**
     * @test
     */
    public function itShouldBeAbleToConvertToString()
    {
        $duration = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(7261000)->build();

        $this->assertEquals("2h 01m 01s", (string) $duration);
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenDurationIsNegative()
    {
        MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(-1.0)->build();
    }

    /**
     * @test
     */
    public function itShouldBeComparableWithOtherDuration()
    {
        $duration = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1000)->build();

        $this->assertEquals(1, $duration->compareTo(
            MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(999)->build()
        ));
        $this->assertEquals(0, $duration->compareTo(
            MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1000)->build()
        ));
        $this->assertEquals(-1, $duration->compareTo(
            MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1001)->build()
        ));
    }

    /**
     * @test
     */
    public function itShouldBeAbeToCalculateDiffWithOtherDuration()
    {
        $duration = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1000)->build();
        $faster = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(800)->build();
        $slower = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1200)->build();

        $this->assertEquals(
            PercentDataBuilder::aPercent()->withValue(0)->build(),
            $duration->diff($duration),
            'Diff with equal duration'
        );

        $this->assertEquals(
            PercentDataBuilder::aPercent()->withValue(0.2)->build(),
            $duration->diff($faster),
            'Diff with faster duration'
        );

        $this->assertEquals(
            PercentDataBuilder::aPercent()->withValue(-0.2)->build(),
            $duration->diff($slower),
            'Diff with slower duration'
        );
    }

    /**
     * @test
     */
    public function itShouldBeAbeToCalculateDiffWhenDurationIsZero()
    {
        $durationZero = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(0)->build();
        $duration = MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(1000)->build();

        $this->assertEquals(
            PercentDataBuilder::aPercent()->withValue(0)->build(),
            $durationZero->diff($duration),
            'Diff duration zero with other duration'
        );

        $this->assertEquals(
            PercentDataBuilder::aPercent()->withValue(1)->build(),
            $duration->diff($durationZero),
            'Diff with a duration zero'
        );
    }
}
