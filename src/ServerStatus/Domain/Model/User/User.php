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

namespace ServerStatus\ServerStatus\Domain\Model\User;

use ServerStatus\Domain\Model\User\UserAlias;
use ServerStatus\Domain\Model\User\UserEmail;
use ServerStatus\Domain\Model\User\UserId;

final class User
{
    private $id;
    private $email;
    private $alias;

    public function __construct(UserId $id, UserEmail $email, UserAlias $alias)
    {
        $this->id = $id;
        $this->email = $email;
        $this->alias = $alias;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function alias(): UserAlias
    {
        return $this->alias;
    }

    public function screenName(): string
    {
        return $this->alias()->isEmpty() ? $this->email()->value() : $this->alias()->value();
    }
}
