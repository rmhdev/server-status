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
        $summary = new MeasureDaySummary(new \DateTime("2018-02-03T15:24:10+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-03T00:00:00+0200"), $summary->from());
    }

    /**
     * @test
     */
    public function itShouldReturnCorrectToDateTime()
    {
        $summary = new MeasureDaySummary(new \DateTime("2018-02-03T15:24:10+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-03T23:59:59+0200"), $summary->to());
    }

    /**
     * @test
     */
    public function itShouldReturnExactNumberOfDates()
    {
        $summary = new MeasureDaySummary(new \DateTime("2018-02-03T15:24:10+0200"));
        $dates = $summary->dates();

        $this->assertEquals(24 * (60 / 10), sizeof($dates));
    }
}
