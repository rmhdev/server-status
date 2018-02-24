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
use ServerStatus\Domain\Model\Customer\CustomerId;

class CustomerIdTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAUniqueIdWhenCreatedEmpty()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("")->build();

        $this->assertInternalType("string", $id->id());
        $this->assertGreaterThan(0, strlen($id->id()));
    }

    /**
     * @test
     */
    public function itShouldHaveValueWhenExplicitlyGiven()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", $id->id());
    }

    /**
     * @test
     */
    public function itShouldReturnValueWhenCastingToString()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", (string) $id);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToBeComparedWithOtherUserIds()
    {
        $id = CustomerIdDataBuilder::aCustomerId()->withValue("loremipsum")->build();

        $this->assertFalse($id->equals(new CustomerId("123456")));
        $this->assertTrue($id->equals($id));
    }
}
