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

use ServerStatus\Infrastructure\Domain\Model\DoctrineEntityId;
use ServerStatus\Domain\Model\Customer\Customer;

class DoctrineCustomerId extends DoctrineEntityId
{
    public function getName()
    {
        return "CustomerId";
    }

    public function getNamespace()
    {
        return substr(Customer::class, 0, -strlen("\Customer"));
    }
}
