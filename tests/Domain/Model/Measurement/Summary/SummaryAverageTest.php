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
use ServerStatus\Domain\Model\Measurement\Summary\SummaryAverage;

class SummaryAverageTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnZeroResponseTimeWhenNoDataIsDefined()
    {
        $summaryAverage = new SummaryAverage();

        $this->assertSame(0.000000, $summaryAverage->responseTime());
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
        $summaryAverage = new SummaryAverage($values);

        $this->assertSame(1.000250, $summaryAverage->responseTime());
    }

    /**
     * @test
     */
    public function itShouldAcceptASingleValueWithData()
    {
        $summaryAverage = new SummaryAverage(["response_time" => 1.000100]);

        $this->assertSame(1.000100, $summaryAverage->responseTime());
    }
}
