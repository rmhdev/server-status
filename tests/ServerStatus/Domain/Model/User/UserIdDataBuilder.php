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

use ServerStatus\Domain\Model\User\UserId;

class UserIdDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "loremipsum";
    }

    public function withValue(string $value): UserIdDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): UserId
    {
        return new UserId($this->value);
    }

    public static function aUserId(): UserIdDataBuilder
    {
        return new self();
    }
}
