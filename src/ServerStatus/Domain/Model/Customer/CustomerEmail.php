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

namespace ServerStatus\Domain\Model\Customer;

class CustomerEmail
{
    private $value;

    public function __construct(string $value = "")
    {
        $processed = $this->processValue($value);
        $this->assertValue($processed);
        $this->value = $processed;
    }

    private function processValue(string $value): string
    {
        return trim(strtolower($value));
    }

    /**
     * @param string $value
     * @throws InvalidCustomerEmailException
     */
    private function assertValue(string $value)
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidCustomerEmailException(sprintf(
                'Email "%s" is not valid',
                $value
            ));
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
