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
            [150, "150 ms"],
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
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenDurationIsNegative()
    {
        MeasurementDurationDataBuilder::aMeasurementDuration()->withDuration(-1.0)->build();
    }
}
