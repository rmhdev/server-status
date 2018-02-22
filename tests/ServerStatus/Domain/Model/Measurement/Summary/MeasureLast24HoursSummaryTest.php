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

namespace ServerStatus\Tests\Domain\Model\Measurement\Summary;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureLast24HoursSummary;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class MeasureLast2HoursSummaryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnTheSummaryName()
    {
        $summary = $this->createSummary();

        $this->assertEquals("last_24_hours", $summary->name());
    }

    private function createSummary($values = [], \DateTimeInterface $dateTime = null): MeasureLast24HoursSummary
    {
        return new MeasureLast24HoursSummary(CheckDataBuilder::aCheck()->build(), $values, $dateTime);
    }

    /**
     * @test
     */
    public function itShouldHaveDefaultDateTime()
    {
        $summary = $this->createSummary();
        $seconds = $summary->to()->getTimestamp() - $summary->from()->getTimestamp();

        $this->assertEquals(24 * 60 * 60, $seconds, "The duration must be exactly 24 hours");
    }

    /**
     * @test
     */
    public function itShouldReturnCorrectFromDateTime()
    {
        $summary = $this->createSummary([], new \DateTime("2018-02-03T15:24:10+0200"));

        $this->assertEquals(
            new \DateTimeImmutable("2018-02-02T15:24:10+0200"),
            $summary->from(),
            'The "from" date must be exactly 24 hours before the given date'
        );
    }

    /**
     * @test
     */
    public function itShouldReturnCorrectToDateTime()
    {
        $summary = $this->createSummary([], new \DateTime("2018-02-03T15:24:10+0200"));

        $this->assertEquals(
            new \DateTimeImmutable("2018-02-03T15:24:10+0200"),
            $summary->to(),
            'The "to" date must be exactly the given date'
        );
    }

    /**
     * @test
     */
    public function itShouldReturnExactNumberOfDates()
    {
        $summary = $this->createSummary([], new \DateTime("2018-02-03T15:24:10+0200"));
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

        $summary = $this->createSummary($rawValues, new \DateTime("2018-02-03T15:24:10+0200"));
        $average = $summary->average(new \DateTime("2018-02-03T15:23:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-03T15:20:00+0200"), $average->from());
        $this->assertEquals(new \DateTimeImmutable("2018-02-03T15:29:59+0200"), $average->to());
        $this->assertEquals(1.000250, $average->responseTime());
    }
}
