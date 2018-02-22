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

namespace ServerStatus\Tests\Domain\Model\Measurement\Performance;

use PHPUnit\Framework\TestCase;
use ServerStatus\ServerStatus\Domain\Model\Measurement\Performance\PerformanceReportWeek;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class PerformanceReportWeekTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnFromAndToDates()
    {
        $performance = $this->createPerformance(new \DateTime("2018-02-22T15:24:10+0200"));

        $this->assertEquals(new \DateTimeImmutable("2018-02-19T00:00:00+0200"), $performance->from());
        $this->assertEquals(new \DateTimeImmutable("2018-02-26T00:00:00+0200"), $performance->to());
    }

    private function createPerformance(\DateTimeInterface $dateTime)
    {
        return new PerformanceReportWeek(
            CheckDataBuilder::aCheck()->build(),
            $dateTime,
            PerformanceDataBuilder::aPerformance()->build()
        );
    }
}
