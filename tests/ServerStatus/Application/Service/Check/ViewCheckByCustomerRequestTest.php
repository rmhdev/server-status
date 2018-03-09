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
use ServerStatus\Application\Service\Check\ViewCheckByCustomerRequest;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Tests\Domain\Model\Check\CheckNameDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class ViewCheckByCustomerRequestTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveADefaultDateWhenEmpty()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->build();
        $name = CheckNameDataBuilder::aCheckName()->build();
        $request = new ViewCheckByCustomerRequest($id, $name);

        $this->assertInstanceOf(\DateTimeImmutable::class, $request->date());
    }

    /**
     * @test
     */
    public function itShouldHaveADefaultMeasureSummaryName()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->build();
        $name = CheckNameDataBuilder::aCheckName()->build();
        $request = new ViewCheckByCustomerRequest($id, $name);

        $this->assertEquals(DateRangeLast24Hours::NAME, $request->dateRange()->name());
    }
}
