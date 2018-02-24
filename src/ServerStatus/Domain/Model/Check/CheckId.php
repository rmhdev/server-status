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

namespace ServerStatus\Domain\Model\Check;

use Ramsey\Uuid\Uuid;

class CheckId
{
    private $id;

    public function __construct(string $id = "")
    {
        $this->id = $id ? $id : Uuid::uuid4()->toString();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id();
    }

    public function equals(CheckId $id): bool
    {
        return $this->id() === $id->id();
    }
}
