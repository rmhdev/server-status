<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Domain\Model\User;

use PHPUnit\Framework\TestCase;
use ServerStatus\Tests\Domain\Model\User\UserEmailDataBuilder;

class UserEmailTest extends TestCase
{
    /**
     * @test
     * @expectedException \ServerStatus\ServerStatus\Domain\Model\User\InvalidUserEmailException
     */
    public function itShouldThrowExceptionWithEmptyEmail()
    {
        UserEmailDataBuilder::aUserEmail()->withValue("")->build();
    }

    /**
     * @expectedException \ServerStatus\ServerStatus\Domain\Model\User\InvalidUserEmailException
     */
    public function itShouldThrowExceptionForIncorrectEmail($incorrectEmail)
    {
        UserEmailDataBuilder::aUserEmail()->withValue($incorrectEmail)->build();
    }

    /**
     * @dataProvider nonSanitizedEmailProvider
     */
    public function itShouldSanitizeEmail(string $raw, string $expected)
    {
        $email = UserEmailDataBuilder::aUserEmail()->withValue($raw)->build();

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
        $email = UserEmailDataBuilder::aUserEmail()->withValue("hello@example.com")->build();

        $this->assertEquals("hello@example.com", (string) $email);
    }
}
