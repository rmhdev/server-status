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

namespace ServerStatus\Tests\Domain\Model\Check;

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckUrl;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class CheckDataBuilder
{
    private $id;
    private $name;
    private $url;
    private $customer;

    public function __construct()
    {
        $this->id = CheckIdDataBuilder::aCheckId()->build();
        $this->name = CheckNameDataBuilder::aCheckName()->build();
        $this->url = CheckUrlDataBuilder::aCheckUrl()->build();
        $this->customer = CustomerDataBuilder::aCustomer()->build();
    }

    public function withId(CheckId $id): CheckDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function withName($name): CheckDataBuilder
    {
        if (is_string($name)) {
            $name = CheckNameDataBuilder::aCheckName()->withName($name)->withSlug("")->build();
        }
        $this->name = $name;

        return $this;
    }

    public function withUrl(CheckUrl $url): CheckDataBuilder
    {
        $this->url = $url;

        return $this;
    }

    public function withCustomer(Customer $customer): CheckDataBuilder
    {
        $this->customer = $customer;

        return $this;
    }

    public function build(): Check
    {
        return new Check($this->id, $this->name, $this->url, $this->customer);
    }

    public static function aCheck(): CheckDataBuilder
    {
        return new self();
    }
}
