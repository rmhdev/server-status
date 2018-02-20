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

namespace ServerStatus\Domain\Model\User;

use Ramsey\Uuid\Uuid;

class UserId
{
    private $value;

    public function __construct(string $value = "")
    {
        $this->value = $value ? $value : Uuid::uuid4()->toString();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function equals(UserId $id): bool
    {
        return $this->value() === $id->value();
    }
}
