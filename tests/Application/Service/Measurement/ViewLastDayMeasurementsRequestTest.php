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
use ServerStatus\Application\Service\Measurement\ViewLastDayMeasurements;

class ViewLastDayMeasurementsRequestTest extends TestCase
{
    public function itShouldHaveADefaultDateWhenEmpty()
    {
        $request = new ViewLastDayMeasurements();

        $this->assertInstanceOf(\DateTimeImmutable::class, $request->date());
    }

    /**
     * @test
     */
    public function itShouldCalculateTheStartingDate()
    {
        $date = "2018-01-31T12:00:00+0200";
        $request = new ViewLastDayMeasurements(new \DateTime($date));

        $this->assertSame("2018-01-30T12:00:00+0200", $request->from()->format(DATE_ISO8601));
    }
}
