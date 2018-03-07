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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeCustom;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class MeasureSummaryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnTheSummaryName()
    {
        $summary = $this->createSummary(new \DateTimeImmutable("now"));

        $this->assertEquals("day", $summary->name());
    }

    private function createSummary(\DateTimeInterface $dateTime, $values = []): MeasureSummary
    {
        return new MeasureSummary(
            CheckDataBuilder::aCheck()->build(),
            $values,
            new DateRangeDay($dateTime)
        );
    }

    /**
     * @test
     */
    public function itShouldReturnExactNumberOfDates()
    {
        $summary = $this->createSummary(new \DateTime("2018-02-03T15:24:10+0200"), []);
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
        $summary = $this->createSummary(new \DateTime("2018-02-03T15:24:10+0200"), $rawValues);
        $average = $summary->average(
            new DateRangeCustom(
                new \DateTime("2018-02-03T15:20:00+0200"),
                new \DateTime("2018-02-03T15:29:00+0200")
            )
        );

        $this->assertEquals(1.000250, $average->responseTime());
    }
}
