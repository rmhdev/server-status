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

namespace ServerStatus\tests\Domain\Model\Measurement\Summary;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureDaySummary;

class MeasureDaySummaryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnTheSummaryName()
    {
        $summary = new MeasureDaySummary();

        $this->assertEquals("day", $summary->name());
    }

    /**
     * @test
     */
    public function itShouldHaveDefaultDateTime()
    {
        $summary = new MeasureDaySummary();

        $this->assertEquals("00:00:00", $summary->from()->format("H:i:s"));
        $this->assertEquals("23:59:59", $summary->to()->format("H:i:s"));
    }

    /**
     * @test
     */
    public function itShouldReturnCorrectFromDateTime()
    {
        $summary = new MeasureDaySummary([], new \DateTime("2018-02-03T15:24:10+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-03T00:00:00+0200"), $summary->from());
    }

    /**
     * @test
     */
    public function itShouldReturnCorrectToDateTime()
    {
        $summary = new MeasureDaySummary([], new \DateTime("2018-02-03T15:24:10+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-03T23:59:59+0200"), $summary->to());
    }

    /**
     * @test
     */
    public function itShouldReturnExactNumberOfDates()
    {
        $summary = new MeasureDaySummary([], new \DateTime("2018-02-03T15:24:10+0200"));
        $averages = $summary->averages();

        $this->assertEquals(24 * (60 / 10), sizeof($averages));
    }

    /**
     * @test
     */
    public function itShouldCalculateCorrectAverages()
    {
        $rawValues = [
            // should ignore previous values: ..19
            [
                "date" => "2018-02-03T15:19:00+0200",
                "response_time" => 1.000400,
                "count" => 1
            ],
            // testing values: minutes 20..29
            [
                "date" => "2018-02-03T15:20:00+0200",
                "response_time" => 1.000100,
                "count" => 1
            ],
            [
                "date" => "2018-02-03T15:21:00+0200",
                "response_time" => 1.000200,
                "count" => 1
            ],
            [
                "date" => "2018-02-03T15:22:00+0200",
                "response_time" => 1.000300,
                "count" => 1
            ],
            [
                "date" => "2018-02-03T15:23:00+0200",
                "response_time" => 1.000400,
                "count" => 1
            ],
            // should ignore next values: 30..
            [
                "date" => "2018-02-03T15:30:00+0200",
                "response_time" => 1.000400,
                "count" => 1
            ],
        ];
        $summary = new MeasureDaySummary($rawValues, new \DateTime("2018-02-03T15:24:10+0200"));
        $average = $summary->average(new \DateTime("2018-02-03T15:23:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-03T15:20:00+0200"), $average->from());
        $this->assertEquals(new \DateTimeImmutable("2018-02-03T15:29:59+0200"), $average->to());
        $this->assertEquals(1.000250, $average->responseTime());
    }
}
