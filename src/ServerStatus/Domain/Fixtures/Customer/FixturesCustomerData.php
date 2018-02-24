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

use ServerStatus\Domain\Model\Customer\CustomerFactory;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Tests\Domain\Model\Customer\CustomerAliasDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerEmailDataBuilder;

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
                    $this->repository->nextId(),
                    CustomerEmailDataBuilder::aCustomerEmail()->withValue($value["email"])->build(),
                    CustomerAliasDataBuilder::aCustomerAlias()->withValue($value["alias"])->build()
                )
            );
        }
    }

    private function customerValues()
    {
        return [
            [
                "email" => "rober@example.com",
                "alias" => "Roberto",
            ],
        ];
    }
}
