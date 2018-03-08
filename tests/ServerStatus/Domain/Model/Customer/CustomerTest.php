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

class CustomerTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnAliasValueAsScreenName()
    {
        $customer = CustomerDataBuilder::aCustomer()->withAlias(
            CustomerAliasDataBuilder::aCustomerAlias()->withValue("my alias")->build()
        )->build();

        $this->assertEquals("my alias", $customer->screenName());
    }

    /**
     * @test
     */
    public function itShouldReturnTheEmailAsScreenNameWhenNoAliasIsDefined()
    {
        $customer = CustomerDataBuilder::aCustomer()
            ->withAlias(CustomerAliasDataBuilder::aCustomerAlias()->withValue("")->build())
            ->withEmail(CustomerEmailDataBuilder::aCustomerEmail()->withValue("name@company.com")->build())
            ->build();

        $this->assertEquals("name@company.com", $customer->screenName());
    }

    /**
     * @test
     */
    public function itShouldBeAbleToBeConvertedToString()
    {
        $customer = CustomerDataBuilder::aCustomer()
            ->withId(CustomerIdDataBuilder::aCustomerId()->withValue("my-id")->build())
            ->withEmail(CustomerEmailDataBuilder::aCustomerEmail()->withValue("username@example.com")->build())
            ->withAlias(CustomerAliasDataBuilder::aCustomerAlias()->withValue("my-username")->build())
            ->build();

        $this->assertEquals("username@example.com (my-id)", (string) $customer);
    }
}
