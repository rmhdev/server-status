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

namespace ServerStatus\Domain\Fixtures\Customer;

use ServerStatus\Domain\Model\Customer\CustomerAlias;
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\CustomerFactory;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Domain\Model\Customer\CustomerStatus;

final class FixturesCustomerData
{
    /**
     * @var CustomerRepository
     */
    private $repository;

    /**
     * @var CustomerFactory
     */
    private $factory;


    public function __construct(CustomerRepository $repository, CustomerFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    public function load()
    {
        foreach ($this->values() as $customer) {
            $this->repository->add($customer);
        }
    }

    /**
     * @return \Generator
     */
    public function values()
    {
        foreach ($this->customerValues() as $value) {
            yield(
                $this->factory->build(
                    new CustomerId($value["id"]),
                    new CustomerEmail($value["email"]),
                    new CustomerAlias($value["alias"]),
                    new CustomerStatus($value["status"])
                )
            );
        }
    }

    private function customerValues()
    {
        return [
            [
                "id" => "rober",
                "email" => "rober@example.com",
                "alias" => "Roberto",
                "status" => CustomerStatus::CODE_ENABLED
            ],
            [
                "id" => "laura",
                "email" => "laura@example.com",
                "alias" => "Laura Test",
                "status" => CustomerStatus::CODE_ENABLED
            ],
            [
                "id" => "alex",
                "email" => "alex@example.com",
                "alias" => "Alex Test",
                "status" => CustomerStatus::CODE_ENABLED
            ],
            [
                "id" => "carol",
                "email" => "carol@example.com",
                "alias" => "Carol Disabled",
                "status" => CustomerStatus::CODE_DISABLED
            ],
        ];
    }
}
