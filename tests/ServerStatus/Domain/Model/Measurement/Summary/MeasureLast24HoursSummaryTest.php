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
use ServerStatus\Domain\Model\Measurement\Summary\MeasureLast24HoursSummary;

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
        return new MeasureLast24HoursSummary($values, $dateTime);
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
}
