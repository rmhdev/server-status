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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeWeek;

class DateRangeWeekTest extends DateRangeTestCase implements DateRangeInterfaceTest
{
    /**
     * @test
     */
    public function isShouldReturnCorrectFromDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-03T12:00:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-01-29T00:00:00+0200"), $dateRange->from());
    }

    protected function createDateRange(\DateTimeInterface $date): DateRange
    {
        return new DateRangeWeek($date);
    }

    /**
     * @test
     */
    public function isShouldReturnCorrectToDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-03T12:00:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-05T00:00:00+0200"), $dateRange->to());
    }

    /**
     * @test
     */
    public function itShouldReturnName()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-03T12:00:00+0200"));

        $this->assertEquals('week', $dateRange->name());
    }

    /**
     * @test
     */
    public function itShouldReturnTheDateFormatted()
    {
        $dateRange = $this->createDateRange(new \DateTime("2017-01-01T12:00:00+0200"));

        $this->assertEquals('2016 W52: Dec 26, Jan 02', $dateRange->formatted());
    }

    /**
     * @test
     */
    public function itShouldSimplifyFormattedDateWhenInTheSameMonth()
    {
        $dateRange = $this->createDateRange(new \DateTime("2017-01-15T12:00:00+0200"));

        $this->assertEquals('2017 W2: Jan 09..16', $dateRange->formatted());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCastToStringWithTheFormattedDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2017-01-01T12:00:00+0200"));

        $this->assertEquals('2016 W52: Dec 26, Jan 02', (string) $dateRange);
    }

    /**
     * @inheritdoc
     */
    protected function expectedDateInterval(): \DateInterval
    {
        return new \DateInterval("PT1H"); // 1 hour
    }

    /**
     * @test
     */
    public function itShouldReturnADateRageCollection()
    {
        $collection = $this->createDateRange(new \DateTime("2018-01-19T12:00:00+0200"));

        $this->assertEquals(7 * (24), $collection->dateRanges()->count());
    }
}
