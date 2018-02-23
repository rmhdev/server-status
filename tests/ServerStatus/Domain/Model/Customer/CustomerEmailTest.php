<?php

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

class CustomerEmailTest extends TestCase
{
    /**
     * @test
     * @expectedException \ServerStatus\ServerStatus\Domain\Model\Customer\InvalidCustomerEmailException
     */
    public function itShouldThrowExceptionWithEmptyEmail()
    {
        CustomerEmailDataBuilder::aCustomerEmail()->withValue("")->build();
    }

    /**
     * @expectedException \ServerStatus\ServerStatus\Domain\Model\Customer\InvalidCustomerEmailException
     */
    public function itShouldThrowExceptionForIncorrectEmail($incorrectEmail)
    {
        CustomerEmailDataBuilder::aCustomerEmail()->withValue($incorrectEmail)->build();
    }

    /**
     * @dataProvider nonSanitizedEmailProvider
     */
    public function itShouldSanitizeEmail(string $raw, string $expected)
    {
        $email = CustomerEmailDataBuilder::aCustomerEmail()->withValue($raw)->build();

        $this->assertSame($expected, $email->value());
    }

    public function nonSanitizedEmailProvider()
    {
        yield(['user@exAMPle.com', 'user@example.com']);
        yield(['USER@EXAMPLE.COM', 'user@example.com']);
        yield(['   user@example.com ', 'user@example.com']);
    }

    /**
     * @test
     */
    public function itShouldBeCastedToString()
    {
        $email = CustomerEmailDataBuilder::aCustomerEmail()->withValue("hello@example.com")->build();

        $this->assertEquals("hello@example.com", (string) $email);
    }
}
