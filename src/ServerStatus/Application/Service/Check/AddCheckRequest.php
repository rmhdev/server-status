<?php
declare(strict_types=1);

/**
 * This file is part of the server-status package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Application\Service\Check;

use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckStatus;
use ServerStatus\Domain\Model\Check\CheckUrl;
use ServerStatus\Domain\Model\Customer\Customer;

class AddCheckRequest
{
    /**
     * @var CheckName
     */
    private $name;

    /**
     * @var CheckUrl
     */
    private $url;

    /**
     * @var CheckStatus
     */
    private $status;

    /**
     * @var Customer
     */
    private $customer;

    public function __construct(CheckName $name, CheckUrl $url, CheckStatus $status, Customer $customer)
    {
        $this->name = $name;
        $this->url = $url;
        $this->status = $status;
        $this->customer = $customer;
    }

    public function name(): CheckName
    {
        return $this->name;
    }

    public function url(): CheckUrl
    {
        return $this->url;
    }

    public function status(): CheckStatus
    {
        return $this->status;
    }

    public function customer(): Customer
    {
        return $this->customer;
    }
}
