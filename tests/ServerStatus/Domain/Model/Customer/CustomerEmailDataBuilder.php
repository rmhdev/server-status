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

namespace ServerStatus\Tests\Domain\Model\Customer;

use ServerStatus\Domain\Model\Customer\CustomerEmail;

class CustomerEmailDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "username@example.com";
    }

    public function withValue(string $value): CustomerEmailDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): CustomerEmail
    {
        return new CustomerEmail($this->value);
    }

    public static function aCustomerEmail(): CustomerEmailDataBuilder
    {
        return new self();
    }
}
