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

namespace ServerStatus\Tests\Domain\Model\Alert\Reason;

use PHPUnit\Framework\TestCase;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementResultDataBuilder;

class AlertReasonDowntimeTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAName()
    {
        $reason = AlertReasonDowntimeDataBuilder::anAlertReason()->build();

        $this->assertEquals("downtime", $reason->name());
    }

    /**
     * @test
     */
    public function itShouldReturnNotAvailableToCorrectMeasurementResults()
    {
        $reason = AlertReasonDowntimeDataBuilder::anAlertReason()->build();

        $this->assertFalse($reason->isAvailable(
            MeasurementResultDataBuilder::aMeasurementResult()->withStatus(100)->build()
        ), "check availability for informational status");
        $this->assertFalse($reason->isAvailable(
            MeasurementResultDataBuilder::aMeasurementResult()->withStatus(200)->build()
        ), "check availability for successful status");
        $this->assertFalse($reason->isAvailable(
            MeasurementResultDataBuilder::aMeasurementResult()->withStatus(301)->build()
        ), "check availability for redirection status");
    }

    /**
     * @test
     */
    public function itShouldReturnNotAvailableToInternalErrorMeasurementResults()
    {
        $reason = AlertReasonDowntimeDataBuilder::anAlertReason()->build();

        $this->assertFalse($reason->isAvailable(
            MeasurementResultDataBuilder::aMeasurementResult()->withStatus(0)->build()
        ), "check availability for our app's internal error");
    }

    /**
     * @test
     */
    public function itShouldReturnAvailableToIncorrectMeasurementResults()
    {
        $reason = AlertReasonDowntimeDataBuilder::anAlertReason()->build();

        $this->assertTrue($reason->isAvailable(
            MeasurementResultDataBuilder::aMeasurementResult()->withStatus(404)->build()
        ), "check availability for client error status");
        $this->assertTrue($reason->isAvailable(
            MeasurementResultDataBuilder::aMeasurementResult()->withStatus(500)->build()
        ), "check availability for server error status");
    }

    /**
     * @test
     */
    public function itShouldBeAbleToBeConvertedToString()
    {
        $reason = AlertReasonDowntimeDataBuilder::anAlertReason()->build();

        $this->assertSame("a downtime", (string) $reason);
    }
}
