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

namespace ServerStatus\Tests\Domain\Model\Common\DateRange;

use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeCustom;

class DateRangeCustomTest extends DateRangeTestCase implements DateRangeInterfaceTest
{
    /**
     * @test
     */
    public function isShouldReturnCorrectFromDate()
    {
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");
        $to = new \DateTimeImmutable("2018-02-18T18:30:15+0200");
        $dateRange = new DateRangeCustom($from, $to);

        $this->assertEquals($from, $dateRange->from());
    }

    protected function createDateRange(\DateTimeInterface $date): DateRange
    {
        return new DateRangeCustom(
            $date,
            \DateTimeImmutable::createFromFormat(
                DATE_ISO8601,
                $date->format(DATE_ISO8601)
            )->modify("+3000 seconds")
        );
    }

    /**
     * @test
     */
    public function isShouldReturnCorrectToDate()
    {
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");
        $to = new \DateTimeImmutable("2018-02-18T18:30:15+0200");
        $dateRange = new DateRangeCustom($from, $to);

        $this->assertEquals($to, $dateRange->to());
    }

    /**
     * @test
     */
    public function itShouldReturnName()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('custom', $dateRange->name());
    }

    /**
     * @test
     */
    public function itShouldReturnTheDateFormatted()
    {
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");
        $to = new \DateTimeImmutable("2018-02-18T18:30:15+0200");
        $dateRange = new DateRangeCustom($from, $to);

        $this->assertEquals('2018-02-18 12:00:00, 2018-02-18 18:30:15', $dateRange->formatted());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCastToStringWithTheFormattedDate()
    {
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");
        $to = new \DateTimeImmutable("2018-02-18T18:30:15+0200");
        $dateRange = new DateRangeCustom($from, $to);

        $this->assertEquals('2018-02-18 12:00:00, 2018-02-18 18:30:15', (string) $dateRange);
    }

    /**
     * @test
     */
    public function itShouldReturnTheNextDateRange()
    {
        $diff = new \DateInterval('PT3000S');
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");
        $to = $from->add($diff);
        $dateRange = new DateRangeCustom($from, $to);
        $expectedNext = new DateRangeCustom($to, $to->add($diff));


        $this->assertEquals(
            $expectedNext->from(),
            $dateRange->next()->from(),
            $dateRange->name() . ", " . $dateRange
        );
        $this->assertEquals(
            $expectedNext->to(),
            $dateRange->next()->to(),
            $dateRange->name() . ", " . $dateRange
        );
    }

    /**
     * @test
     */
    public function itShouldReturnThePreviousDateRange()
    {
        $diff = new \DateInterval('PT3000S');
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");
        $to = $from->add($diff);
        $dateRange = new DateRangeCustom($from, $to);
        $expectedPrevious = new DateRangeCustom($from->sub($diff), $from);

        $this->assertEquals(
            $expectedPrevious->from(),
            $dateRange->previous()->from(),
            $dateRange->name() . ", " . $dateRange
        );
        $this->assertEquals(
            $expectedPrevious->to(),
            $dateRange->previous()->to(),
            $dateRange->name() . ", " . $dateRange
        );
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionIfFromIsLowerThanTo()
    {
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");

        new DateRangeCustom($from, $from->modify("-1 second"));
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionIfDatesAreEqual()
    {
        $from = new \DateTimeImmutable("2018-02-18T12:00:00+0200");

        new DateRangeCustom($from, $from);
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionIfDatesHaveDifferentTimezones()
    {
        new DateRangeCustom(
            new \DateTimeImmutable("2018-02-18T12:00:00+0200"),
            new \DateTimeImmutable("2018-02-20T16:00:00+0100")
        );
    }
}
