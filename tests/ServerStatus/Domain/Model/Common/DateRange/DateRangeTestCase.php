<?php

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
use ServerStatus\Domain\Model\Common\DateRange\DateRange;

abstract class DateRangeTestCase extends TestCase
{
    abstract protected function createDateRange(\DateTimeInterface $dateTime): DateRange;

    private function newDateRange()
    {
        return $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));
    }

    /**
     * @test
     */
    public function itShouldDetectWhenADateIsInBounds()
    {
        $dateRange = $this->newDateRange();

        $this->assertTrue($dateRange->isInBounds($dateRange->to()->modify("-1 second")));
    }

    /**
     * @test
     */
    public function itShouldDetectWhenALowerDateIsNotInBounds()
    {
        $dateRange = $this->newDateRange();

        $this->assertFalse($dateRange->isInBounds($dateRange->to()));
    }

    /**
     * @test
     */
    public function itShouldDetectWhenAGreaterDateIsNotInBounds()
    {
        $dateRange = $this->newDateRange();

        $this->assertFalse($dateRange->isInBounds($dateRange->from()->modify("-1 second")));
    }

    /**
     * @test
     */
    public function itShouldReturnTheNextDateRange()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));
        $expectedNext = $this->createDateRange($dateRange->to());

        $this->assertEquals($expectedNext->from(), $dateRange->next()->from());
        $this->assertEquals($expectedNext->to(), $dateRange->next()->to());
    }

    /**
     * @test
     */
    public function itShouldReturnThePreviousDateRange()
    {
        $dateRange = $this->createDateRange(new \DateTime("2018-02-19T12:00:00+0200"));
        $expectedPrevious = $this->createDateRange($dateRange->from()->modify("-1 second"));

        $this->assertEquals($expectedPrevious->from(), $dateRange->previous()->from());
        $this->assertEquals($expectedPrevious->to(), $dateRange->previous()->to());
    }
}
