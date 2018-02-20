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

namespace ServerStatus\Tests\Domain\Model\User;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\User\UserId;

class UserIdTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAUniqueIdWhenCreatedEmpty()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("")->build();

        $this->assertInternalType("string", $id->value());
        $this->assertGreaterThan(0, strlen($id->value()));
    }

    /**
     * @test
     */
    public function itShouldHaveValueWhenExplicitlyGiven()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", $id->value());
    }

    /**
     * @test
     */
    public function itShouldReturnValueWhenCastingToString()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", (string) $id);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToBeComparedWithOtherUserIds()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("loremipsum")->build();

        $this->assertFalse($id->equals(new UserId("123456")));
        $this->assertTrue($id->equals($id));
    }
}
