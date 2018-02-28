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

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeMonth;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeWeek;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeYear;

class DateRangeFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnTheDifferentDateRangeNames()
    {
        $this->assertEquals($this->expectedDateRangeNames(), DateRangeFactory::names());
    }

    public function expectedDateRangeNames()
    {
        return array_map(function ($item) {
            return $item[1];
        }, $this->expectedDateRanges());
    }

    public function expectedDateRanges()
    {
        return [
            [DateRangeYear::class, 'year'],
            [DateRangeMonth::class, 'month'],
            [DateRangeWeek::class, 'week'],
            [DateRangeDay::class, 'day'],
            [DateRangeLast24Hours::class, 'last_24_hours']
        ];
    }

    /**
     * @test
     * @dataProvider expectedDateRanges
     */
    public function itShouldCreateDateRangeByName($class, $name)
    {
        $this->assertInstanceOf($class, DateRangeFactory::create($name, new \DateTime("2018-02-19T12:00:00+0200")));
    }

    /**
     * @test
     * @dataProvider incorrectDateRangeNames
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenUnknownNameIsGiven($name)
    {
        DateRangeFactory::create($name, new \DateTime("2018-02-19T12:00:00+0200"));
    }

    public function incorrectDateRangeNames()
    {
        return [
            [""],
            ["date"],
        ];
    }
}
