<?php
declare(strict_types=1);

/**
 * This file is part of the bidaia package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Infrastructure\Domain\Model\Customer;

use ServerStatus\Domain\Model\Customer\CustomerAlias;
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\CustomerFactory;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerStatus;

class DoctrineCustomerFactory implements CustomerFactory
{
    public function build(
        CustomerId $id,
        CustomerEmail $email,
        CustomerAlias $alias,
        CustomerStatus $status
    ): Customer {
        return new Customer($id, $email, $alias, $status);
    }
}
