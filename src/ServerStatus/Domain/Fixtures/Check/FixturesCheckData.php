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

namespace ServerStatus\Domain\Fixtures\Check;

use ServerStatus\Domain\Model\Check\CheckFactory;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Check\CheckUrl;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;

final class FixturesCheckData
{
    /**
     * @var CheckRepository
     */
    private $repository;

    /**
     * @var CheckFactory
     */
    private $factory;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;


    public function __construct(
        CheckRepository $repository,
        CheckFactory $factory,
        CustomerRepository $customerRepository
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->customerRepository = $customerRepository;
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
                    new CheckId($value["id"]),
                    new CheckName($value["name"], $value["slug"]),
                    new CheckUrl($value["method"], $value["protocol"], $value["domain"]),
                    $this->customerRepository->ofId(new CustomerId($value["customer"]["id"]))
                )
            );
        }
    }

    private function customerValues()
    {
        return [
            [
                "id" => "rober-check-first",
                "name" => "My first check",
                "slug" => "my-first-check",
                "method" => "get",
                "protocol" => "https",
                "domain" => "www.google.es",
                "customer" => [
                    "id" => "rober"
                ]
            ],
        ];
    }
}
