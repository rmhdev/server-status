<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Domain\Model\Customer;

use ServerStatus\Domain\Model\Customer\CustomerStatus;

class CustomerStatusDataBuilder
{
    private $code;

    public function __construct()
    {
        $this->code = "";
    }

    public function withValue($code): CustomerStatusDataBuilder
    {
        $this->code = $code;

        return $this;
    }

    public function build(): CustomerStatus
    {
        return new CustomerStatus($this->code);
    }

    public static function aCustomerStatus(): CustomerStatusDataBuilder
    {
        return new self();
    }
}
