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

use ServerStatus\Domain\Model\Customer\CustomerAlias;

class CustomerAliasDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "loremipsum";
    }

    public function withValue(string $value): CustomerAliasDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): CustomerAlias
    {
        return new CustomerAlias($this->value);
    }

    public static function aCustomerAlias(): CustomerAliasDataBuilder
    {
        return new self();
    }
}
