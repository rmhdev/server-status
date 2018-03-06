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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;

class DateRangeLast24HoursTest extends DateRangeTestCase implements DateRangeInterfaceTest
{
    /**
     * @test
     */
    public function isShouldReturnCorrectFromDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:05:12+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-18T12:00:00+0200"), $dateRange->from());
    }

    protected function createDateRange(\DateTimeInterface $date): DateRange
    {
        return new DateRangeLast24Hours($date);
    }

    /**
     * @test
     */
    public function isShouldReturnCorrectToDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:05:12+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-19T12:00:00+0200"), $dateRange->to());
    }

    /**
     * @test
     */
    public function itShouldReturnName()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('last_24_hours', $dateRange->name());
    }

    /**
     * @test
     */
    public function itShouldReturnTheDateFormatted()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('2018-02-18 12:00, 2018-02-19 12:00', $dateRange->formatted());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCastToStringWithTheFormattedDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('2018-02-18 12:00, 2018-02-19 12:00', (string) $dateRange);
    }

    /**
     * @test
     */
    public function itShouldReturnTheNextDateRange()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));
        $expectedNext = $this->createDateRange($dateRange->to()->modify("+1 day"));

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
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));
        $expectedPrevious = $this->createDateRange($dateRange->from()->modify("-1 day"));

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
     * @inheritdoc
     */
    protected function expectedDateInterval(): \DateInterval
    {
        return new \DateInterval("PT600S"); // 10 minutes
    }
}
