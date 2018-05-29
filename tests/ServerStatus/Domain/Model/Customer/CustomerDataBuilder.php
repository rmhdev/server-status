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
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\Customer;

class CustomerDataBuilder
{
    private $id;
    private $email;
    private $alias;
    private $status;

    public function __construct()
    {
        $this->id = CustomerIdDataBuilder::aCustomerId()->build();
        $this->email = CustomerEmailDataBuilder::aCustomerEmail()->build();
        $this->alias = CustomerAliasDataBuilder::aCustomerAlias()->build();
        $this->status = CustomerStatusDataBuilder::aCustomerStatus()->build();
    }

    public function withId($id): CustomerDataBuilder
    {
        if (is_string($id)) {
            $id = CustomerIdDataBuilder::aCustomerId()->withValue($id)->build();
        }
        $this->id = $id;

        return $this;
    }

    public function withEmail($email): CustomerDataBuilder
    {
        if (!$email instanceof CustomerEmail) {
            $email = CustomerEmailDataBuilder::aCustomerEmail()->withValue($email)->build();
        }
        $this->email = $email;

        return $this;
    }

    public function withAlias(CustomerAlias $alias): CustomerDataBuilder
    {
        $this->alias = $alias;

        return $this;
    }

    public function withStatus($status): CustomerDataBuilder
    {
        if (is_string($status)) {
            $status = CustomerStatusDataBuilder::aCustomerStatus()->withValue($status)->build();
        }
        $this->status = $status;

        return $this;
    }

    public function build(): Customer
    {
        return new Customer($this->id, $this->email, $this->alias, $this->status);
    }

    public static function aCustomer(): CustomerDataBuilder
    {
        return new self();
    }
}
