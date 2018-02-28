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
use ServerStatus\Application\Service\Measurement\ViewMeasurementSummaryRequest;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class ViewMeasurementSummaryRequestTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveADefaultDateWhenEmpty()
    {
        $check = CheckDataBuilder::aCheck()->build();
        $request = new ViewMeasurementSummaryRequest($check);

        $this->assertInstanceOf(DateRangeLast24Hours::class, $request->dateRange());
    }
}
