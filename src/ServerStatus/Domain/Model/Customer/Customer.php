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

namespace ServerStatus\ServerStatus\Domain\Model\Customer;

use ServerStatus\Domain\Model\Customer\CustomerAlias;
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\CustomerId;

final class Customer
{
    private $id;
    private $email;
    private $alias;

    public function __construct(CustomerId $id, CustomerEmail $email, CustomerAlias $alias)
    {
        $this->id = $id;
        $this->email = $email;
        $this->alias = $alias;
    }

    public function id(): CustomerId
    {
        return $this->id;
    }

    public function email(): CustomerEmail
    {
        return $this->email;
    }

    public function alias(): CustomerAlias
    {
        return $this->alias;
    }

    public function screenName(): string
    {
        return $this->alias()->isEmpty() ? $this->email()->value() : $this->alias()->value();
    }
}
