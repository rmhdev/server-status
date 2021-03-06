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

namespace ServerStatus\Tests\Domain\Model\Customer;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Customer\CustomerStatus;

class CustomerStatusTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBeEnabledByDefault()
    {
        $status = CustomerStatusDataBuilder::aCustomerStatus()->withValue("")->build();

        $this->assertTrue($status->isEnabled());
    }

    /**
     * @test
     */
    public function itShouldBeDisabledWhenDefined()
    {
        $status = CustomerStatusDataBuilder::aCustomerStatus()->withValue(CustomerStatus::CODE_DISABLED)->build();

        $this->assertFalse($status->isEnabled());
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Customer\InvalidCustomerStatusException
     */
    public function itShouldThrowExceptionWhenIncorrectCodeIsUsed()
    {
        CustomerStatusDataBuilder::aCustomerStatus()->withValue("lorem")->build();
    }
}
