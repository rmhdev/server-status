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

namespace ServerStatus\Tests\Application\Service\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\Service\Check\ViewChecksByCustomerRequest;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureLast24HoursSummary;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class ViewChecksByUserRequestTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveADefaultDateWhenEmpty()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->build();
        $request = new ViewChecksByCustomerRequest($id);

        $this->assertInstanceOf(\DateTimeImmutable::class, $request->date());
    }

    /**
     * @test
     */
    public function itShouldHaveADefaultMeasureSummaryName()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->build();
        $request = new ViewChecksByCustomerRequest($id);

        $this->assertEquals(MeasureLast24HoursSummary::NAME, $request->name());
    }
}
