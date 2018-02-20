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

use ServerStatus\Domain\Model\User\UserAlias;
use ServerStatus\Domain\Model\User\UserEmail;
use ServerStatus\Domain\Model\User\UserId;
use ServerStatus\ServerStatus\Domain\Model\User\User;

class UserDataBuilder
{
    private $id;
    private $email;
    private $alias;

    public function __construct()
    {
        $this->id = UserIdDataBuilder::aUserId()->build();
        $this->email = UserEmailDataBuilder::aUserEmail()->build();
        $this->alias = UserAliasDataBuilder::aUserAlias()->build();
    }

    public function withId(UserId $id): UserDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function withEmail(UserEmail $email): UserDataBuilder
    {
        $this->email = $email;

        return $this;
    }

    public function withAlias(UserAlias $alias): UserDataBuilder
    {
        $this->alias = $alias;

        return $this;
    }

    public function build(): User
    {
        return new User($this->id, $this->email, $this->alias);
    }

    public static function aUser(): UserDataBuilder
    {
        return new self();
    }
}
