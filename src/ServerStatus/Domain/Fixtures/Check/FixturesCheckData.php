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
use ServerStatus\Domain\Model\Check\CheckStatus;
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
                    new CheckStatus($value["status"]),
                    $this->customerRepository->ofId(new CustomerId($value["customer"]["id"]))
                )
            );
        }
    }

    private function customerValues()
    {
        return [
            [
                "id" => "rober-check-1",
                "name" => "My first check",
                "slug" => "my-first-check",
                "method" => "get",
                "protocol" => "https",
                "domain" => "www.google.es",
                "status" => CheckStatus::CODE_ENABLED,
                "customer" => [
                    "id" => "rober"
                ]
            ],
            [
                "id" => "rober-check-2",
                "name" => "My second check",
                "slug" => "my-second-check",
                "method" => "get",
                "protocol" => "https",
                "domain" => "www.yahoo.com",
                "status" => CheckStatus::CODE_ENABLED,
                "customer" => [
                    "id" => "rober"
                ]
            ],
            [
                "id" => "rober-check-disabled",
                "name" => "My disabled check",
                "slug" => "my-disabled-check",
                "method" => "get",
                "protocol" => "https",
                "domain" => "www.google.com",
                "status" => CheckStatus::CODE_DISABLED,
                "customer" => [
                    "id" => "rober"
                ]
            ],
            [
                "id" => "laura-check-1",
                "name" => "My first check",
                "slug" => "my-first-check",
                "method" => "get",
                "protocol" => "https",
                "domain" => "github.com",
                "status" => CheckStatus::CODE_ENABLED,
                "customer" => [
                    "id" => "laura"
                ]
            ],
            [
                "id" => "carol-check-1",
                "name" => "My first check",
                "slug" => "my-first-check",
                "method" => "get",
                "protocol" => "https",
                "domain" => "bitbucket.org",
                "status" => CheckStatus::CODE_ENABLED,
                "customer" => [
                    "id" => "carol"
                ]
            ],
        ];
    }
}
