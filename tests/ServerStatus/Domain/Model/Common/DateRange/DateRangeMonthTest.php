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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeMonth;

class DateRangeMonthTest extends DateRangeTestCase implements DateRangeInterfaceTest
{
    /**
     * @test
     */
    public function isShouldReturnCorrectFromDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-01T00:00:00+0200"), $dateRange->from());
    }

    protected function createDateRange(\DateTimeInterface $date): DateRange
    {
        return new DateRangeMonth($date);
    }

    /**
     * @test
     */
    public function isShouldReturnCorrectToDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-03-01T00:00:00+0200"), $dateRange->to());
    }

    /**
     * @test
     */
    public function itShouldReturnName()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('month', $dateRange->name());
    }

    /**
     * @test
     */
    public function itShouldReturnTheDateFormatted()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('2018-02', $dateRange->formatted());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCastToStringWithTheFormattedDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('2018-02', (string) $dateRange);
    }

    /**
     * @inheritdoc
     */
    protected function expectedDateInterval(): \DateInterval
    {
        return new \DateInterval("PT4H"); // 4 hours
    }

    /**
     * @test
     */
    public function itShouldReturnADateRageCollection()
    {
        $collection = $this->createDateRange(new \DateTime("2018-01-19T12:00:00+0200"));

        $this->assertEquals(31 * (24 / 4), $collection->dateRanges()->count());
    }
}
