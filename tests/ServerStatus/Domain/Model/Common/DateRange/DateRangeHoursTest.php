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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeHours;

class DateRangeHoursTest extends DateRangeTestCase implements DateRangeInterfaceTest
{
    /**
     * @test
     */
    public function isShouldReturnCorrectFromDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T11:15:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-19T11:00:00+0200"), $dateRange->from());
    }

    protected function createDateRange(\DateTimeInterface $date): DateRange
    {
        return new DateRangeHours($date);
    }

    /**
     * @test
     */
    public function isShouldReturnCorrectToDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T11:15:00+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-19T12:00:00+0200"), $dateRange->to());
    }

    /**
     * @test
     */
    public function itShouldReturnName()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals('hours', $dateRange->name());
    }

    /**
     * @test
     */
    public function itShouldReturnTheDateFormatted()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T11:00:00+0200"));

        $this->assertEquals('2018-02-19, 11:00-12:00', $dateRange->formatted());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCustomizeAmountOfHours()
    {
        $dateRange = new DateRangeHours(new \DateTime("2018-02-19T11:15:00+0200"), 10);

        $this->assertEquals(new \DateTimeImmutable("2018-02-19T21:00:00+0200"), $dateRange->to());
    }

    /**
     * @test
     * @dataProvider incorrectHourValues
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenIncorrectHourValueIsAdded($hours)
    {
        new DateRangeHours(new \DateTime("2018-02-19T11:15:00+0200"), $hours);
    }

    public function incorrectHourValues()
    {
        return [
            [0],
            [-1],
        ];
    }

    /**
     * @test
     */
    public function itShouldBeAbleToCastToStringWithTheFormattedDate()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T11:00:00+0200"));

        $this->assertEquals('2018-02-19, 11:00-12:00', (string) $dateRange);
    }

    /**
     * @inheritdoc
     */
    protected function expectedDateInterval(): \DateInterval
    {
        return new \DateInterval("PT1H");
    }

    /**
     * @test
     */
    public function itShouldReturnADateRageCollection()
    {
        $collection = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));

        $this->assertEquals(60 / 10, $collection->dateRanges()->count());
    }
}
