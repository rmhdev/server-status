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

namespace ServerStatus\Application\DataTransformer\Alert;

use ServerStatus\Domain\Model\Customer\Customer;

final class CustomerAlertsDtoDataTransformer implements CustomerAlertsDataTransformer
{
    /**
     * @var Customer
     */
    private $customer;

    public function write(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function read()
    {
        return [
            "customer" => $this->readCustomer(),
        ];
    }

    private function readCustomer()
    {
        return [
            "id" => $this->customer->id()->id(),
            "email" => $this->customer->email()->value(),
            "screen_name" => $this->customer->screenName(),
            "alias" => $this->customer->alias()->value(),
        ];
    }
}
