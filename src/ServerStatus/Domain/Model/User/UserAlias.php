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

use ServerStatus\ServerStatus\Domain\Model\User\InvalidUserAliasException;

class UserAlias
{
    const MAX_LENGTH = 20;

    private $value;

    public function __construct(string $value = "")
    {
        $processed = $this->processValue($value);
        $this->assertValue($processed);
        $this->value = $processed;
    }

    private function processValue(string $value): string
    {
        return trim($value);
    }

    /**
     * @param string $value
     * @throws InvalidUserAliasException
     */
    private function assertValue(string $value)
    {
        $length = mb_strlen($value);
        if (self::MAX_LENGTH < $length) {
            throw new InvalidUserAliasException(sprintf(
                'Alias "%s" is too long (%d chars), max length is %d chars',
                $value,
                $length,
                self::MAX_LENGTH
            ));
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return "" == $this->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
