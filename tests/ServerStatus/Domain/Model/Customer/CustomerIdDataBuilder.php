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

use ServerStatus\Domain\Model\Customer\CustomerId;

class CustomerIdDataBuilder
{
    private $value;

    public function __construct()
    {
        $this->value = "";
    }

    public function withValue(string $value): CustomerIdDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build(): CustomerId
    {
        return new CustomerId($this->value);
    }

    public static function aCustomerId(): CustomerIdDataBuilder
    {
        return new self();
    }
}
