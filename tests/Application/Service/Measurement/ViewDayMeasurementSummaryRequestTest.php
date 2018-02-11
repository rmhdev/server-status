<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Application\Service\Measurement;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\Service\Measurement\ViewDayMeasurementSummaryRequest;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class ViewDayMeasurementSummaryRequestTest extends TestCase
{
    public function itShouldHaveADefaultDateWhenEmpty()
    {
        $check = CheckDataBuilder::aCheck()->build();
        $request = new ViewDayMeasurementSummaryRequest($check);

        $this->assertInstanceOf(\DateTimeImmutable::class, $request->date());
    }

    /**
     * @test
     */
    public function itShouldCalculateTheStartingDate()
    {
        $check = CheckDataBuilder::aCheck()->build();
        $date = "2018-01-31T12:00:00+0200";
        $request = new ViewDayMeasurementSummaryRequest($check, new \DateTime($date));

        $this->assertSame("2018-01-31T00:00:00+0200", $request->from()->format(DATE_ISO8601));
        $this->assertSame("2018-01-31T23:59:59+0200", $request->to()->format(DATE_ISO8601));
    }
}
