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

use ServerStatus\Domain\Model\User\UserEmail;

class UserEmailDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "username@example.com";
    }

    public function withValue(string $value): UserEmailDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): UserEmail
    {
        return new UserEmail($this->value);
    }

    public static function aUserEmail(): UserEmailDataBuilder
    {
        return new self();
    }
}
