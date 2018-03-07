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

namespace ServerStatus\Tests\Domain\Model\Alert;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeMinutes;

class AlertTimeWindowTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveADefaultValue()
    {
        $window = AlertTimeWindowDataBuilder::anAlertTimeWindow()->build();

        $this->assertSame(5, $window->minutes());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     * @dataProvider incorrectMinutes
     */
    public function itShouldThrowExceptionWhenIncorrectMinutesAreUsed($minutes)
    {
        AlertTimeWindowDataBuilder::anAlertTimeWindow()->withValue($minutes)->build();
    }

    public function incorrectMinutes()
    {
        return [
            [-1],
            [0],
            [3],
            [61],
        ];
    }

    /**
     * @test
     * @dataProvider expectedDateRangesForWindowSize
     */
    public function itShouldCalculateADateRangeGivenADate($minutes, \DateTimeImmutable $date, DateRange $expected)
    {
        $window = AlertTimeWindowDataBuilder::anAlertTimeWindow()->withValue($minutes)->build();

        $this->assertEquals($expected, $window->dateRange($date), "Date range for {$minutes} minutes");
    }

    public function expectedDateRangesForWindowSize()
    {
        $date = new \DateTimeImmutable("2018-02-19T12:56:34+0200");

        return [
            [
                5,
                $date,
                new DateRangeMinutes(new \DateTime("2018-02-19T12:55:00+0200"), 5)
            ],
            [
                10,
                $date,
                new DateRangeMinutes(new \DateTime("2018-02-19T12:50:00+0200"), 10)
            ],
            [
                15,
                $date,
                new DateRangeMinutes(new \DateTime("2018-02-19T12:45:00+0200"), 15)
            ],
            [
                30,
                $date,
                new DateRangeMinutes(new \DateTime("2018-02-19T12:30:00+0200"), 30)
            ],
            [
                60,
                $date,
                new DateRangeMinutes(new \DateTime("2018-02-19T12:00:00+0200"), 60)
            ],
        ];
    }
}
