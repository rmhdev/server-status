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

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnAliasValueAsScreenName()
    {
        $user = UserDataBuilder::aUser()->withAlias(
            UserAliasDataBuilder::aUserAlias()->withValue("my alias")->build()
        )->build();

        $this->assertEquals("my alias", $user->screenName());
    }

    /**
     * @test
     */
    public function itShouldReturnTheEmailAsScreenNameWhenNoAliasIsDefined()
    {
        $user = UserDataBuilder::aUser()
            ->withAlias(UserAliasDataBuilder::aUserAlias()->withValue("")->build())
            ->withEmail(UserEmailDataBuilder::aUserEmail()->withValue("name@company.com")->build())
            ->build();

        $this->assertEquals("name@company.com", $user->screenName());
    }
}
