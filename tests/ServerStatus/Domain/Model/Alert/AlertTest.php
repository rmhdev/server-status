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
use ServerStatus\Domain\Model\Check\CheckStatus;
use ServerStatus\Domain\Model\Customer\CustomerStatus;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckStatusDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerStatusDataBuilder;

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

    /**
     * @test
     */
    public function itShouldHaveSameStatusAsCustomerWhenNoCheckIsDefined()
    {
        $customerEnabled = CustomerDataBuilder::aCustomer()->withStatus(
            CustomerStatusDataBuilder::aCustomerStatus()->withValue(CustomerStatus::CODE_ENABLED)->build()
        )->build();
        $customerDisabled = CustomerDataBuilder::aCustomer()->withStatus(
            CustomerStatusDataBuilder::aCustomerStatus()->withValue(CustomerStatus::CODE_DISABLED)->build()
        )->build();


        $this->assertTrue(
            AlertDataBuilder::anAlert()->withCustomer($customerEnabled)->build()->isEnabled()
        );
        $this->assertFalse(
            AlertDataBuilder::anAlert()->withCustomer($customerDisabled)->build()->isEnabled()
        );
    }

    /**
     * @test
     */
    public function itShouldTellItIsNotEnabledWhenCustomerIsNoEnabled()
    {
        $customerDisabledB = CustomerDataBuilder::aCustomer()->withStatus(
            CustomerStatusDataBuilder::aCustomerStatus()->withValue(CustomerStatus::CODE_DISABLED)->build()
        )->build();
        $checkEnabledB = CheckDataBuilder::aCheck()->withCustomer($customerDisabledB)->build();
        $checkDisabledB = CheckDataBuilder::aCheck()->withCustomer($customerDisabledB)->withStatus(
            CheckStatusDataBuilder::aCheckStatus()->withCode(CheckStatus::CODE_DISABLED)->build()
        )->build();
        $alertB1 = AlertDataBuilder::anAlert()->withCustomer($customerDisabledB)->withCheck(null)->build();
        $alertB2 = AlertDataBuilder::anAlert()->withCustomer($customerDisabledB)->withCheck($checkEnabledB)->build();
        $alertB3 = AlertDataBuilder::anAlert()->withCustomer($customerDisabledB)->withCheck($checkDisabledB)->build();

        $this->assertFalse($alertB1->isEnabled(), "Alert with disabled customer and no check");
        $this->assertFalse($alertB2->isEnabled(), "Alert with disabled customer and enabled check");
        $this->assertFalse($alertB3->isEnabled(), "Alert with disabled customer and disabled check");
    }

    /**
     * @test
     */
    public function itShouldTellItIsEnabledOnlyWhenCustomerAndCheckAreEnabled()
    {
        $customerEnabledA = CustomerDataBuilder::aCustomer()->withStatus(
            CustomerStatusDataBuilder::aCustomerStatus()->withValue(CustomerStatus::CODE_ENABLED)->build()
        )->build();
        $checkEnabledA = CheckDataBuilder::aCheck()->withCustomer($customerEnabledA)->build();
        $checkDisabledA = CheckDataBuilder::aCheck()->withCustomer($customerEnabledA)->withStatus(
            CheckStatusDataBuilder::aCheckStatus()->withCode(CheckStatus::CODE_DISABLED)->build()
        )->build();
        $alertA1 = AlertDataBuilder::anAlert()->withCustomer($customerEnabledA)->withCheck(null)->build();
        $alertA2 = AlertDataBuilder::anAlert()->withCustomer($customerEnabledA)->withCheck($checkEnabledA)->build();
        $alertA3 = AlertDataBuilder::anAlert()->withCustomer($customerEnabledA)->withCheck($checkDisabledA)->build();

        $this->assertTrue($alertA1->isEnabled(), "Alert with enabled customer and no check");
        $this->assertTrue($alertA2->isEnabled(), "Alert with enabled customer and enabled check");
        $this->assertFalse($alertA3->isEnabled(), "Alert with enabled customer and disabled check");
    }
}
