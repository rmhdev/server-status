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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeYear;

class DateRangeYearTest extends DateRangeTestCase implements DateRangeInterfaceTest
{
    /**
     * @test
     */
    public function isShouldReturnCorrectFromDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-01-01T00:00:00+0200"), $dateRange->from());
    }

    protected function createDateRange(\DateTimeInterface $date): DateRange
    {
        return new DateRangeYear($date);
    }

    /**
     * @test
     */
    public function isShouldReturnCorrectToDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2019-01-01T00:00:00+0200"), $dateRange->to());
    }

    /**
     * @test
     */
    public function itShouldReturnName()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('year', $dateRange->name());
    }

    /**
     * @test
     */
    public function itShouldReturnTheDateFormatted()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('2018', $dateRange->formatted());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCastToStringWithTheFormattedDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('2018', (string) $dateRange);
    }

    /**
     * @inheritdoc
     */
    protected function expectedDateInterval(): \DateInterval
    {
        return new \DateInterval("P1D"); // 1 day
    }

    /**
     * @test
     */
    public function itShouldReturnADateRageCollection()
    {
        $collection = $this->createDateRange(new \DateTime("2018-01-19T12:00:00+0200"));

        $this->assertEquals(365, $collection->dateRanges()->count());
    }
}
