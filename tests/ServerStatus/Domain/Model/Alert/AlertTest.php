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
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class AlertTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBeAbleToBeConvertedToString()
    {
        $alert = AlertDataBuilder::anAlert()->build();

        $this->assertStringStartsWith("If ", (string) $alert);
    }

    /**
     * @test
     */
    public function itShouldAcceptNotDefiningACheck()
    {
        $alert = AlertDataBuilder::anAlert()->build();

        $this->assertNull($alert->check());
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Check\InvalidCheckException
     */
    public function itShouldThrowExceptionWhenGivenCheckIsNotRelatedToCustomer()
    {
        $customer = CustomerDataBuilder::aCustomer()->build();
        $check = CheckDataBuilder::aCheck()->build();

        AlertDataBuilder::anAlert()->withCustomer($customer)->withCheck($check)->build();
    }
}
