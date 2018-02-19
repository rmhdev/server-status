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

class MeasurementResultTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBeSuccessfulWhenResponseCodeIsCorrect()
    {
        $result = MeasurementResultDataBuilder::aMeasurementResult()->withCode(200)->build();

        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @test
     */
    public function itShouldNotBeSuccessfulWhenResponseCodeIsInformational()
    {
        $result = MeasurementResultDataBuilder::aMeasurementResult()->withCode(100)->build();

        $this->assertFalse($result->isSuccessful());
    }

    /**
     * @test
     */
    public function itShouldNotBeSuccessfulWhenResponseCodeIsClientError()
    {
        $result = MeasurementResultDataBuilder::aMeasurementResult()->withCode(404)->build();

        $this->assertFalse($result->isSuccessful());
    }

    /**
     * @test
     */
    public function itShouldNotBeSuccessfulWhenResponseCodeIsServerError()
    {
        $result = MeasurementResultDataBuilder::aMeasurementResult()->withCode(500)->build();

        $this->assertFalse($result->isSuccessful());
    }
}
