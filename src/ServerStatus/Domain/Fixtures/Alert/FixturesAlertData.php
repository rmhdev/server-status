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

namespace ServerStatus\Domain\Fixtures\Alert;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Alert\AlertFactory;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\Alert\AlertTimeWindow;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannelEmail;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannelFactory;
use ServerStatus\Domain\Model\Alert\Reason\AlertReasonDowntime;
use ServerStatus\Domain\Model\Alert\Reason\AlertReasonFactory;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;

final class FixturesAlertData
{


    /**
     * @var AlertRepository
     */
    private $repository;

    /**
     * @var AlertFactory
     */
    private $factory;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var CheckRepository
     */
    private $checkRepository;


    public function __construct(
        AlertRepository $repository,
        AlertFactory $factory,
        CustomerRepository $customerRepository,
        CheckRepository $checkRepository
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->customerRepository = $customerRepository;
        $this->checkRepository = $checkRepository;
    }

    public function load()
    {
        foreach ($this->values() as $alert) {
            $this->repository->add($alert);
        }
    }

    /**
     * @return \Generator|Alert[]
     * @throws \ServerStatus\Domain\Model\Alert\Channel\InvalidAlertChannelException
     * @throws \ServerStatus\Domain\Model\Alert\Reason\InvalidAlertReasonException
     */
    public function values()
    {
        foreach ($this->alertValues() as $value) {
            $customer = $this->findCustomer($value["customer_id"]);
            $check = $this->findCheck($value["check_id"]);

            //print_r($customer ? $customer->id()->id() : "NO CUSTOMER");
            yield(
                $this->factory->build(
                    new AlertId($value["id"]),
                    new AlertTimeWindow($value["window_minutes"]),
                    AlertReasonFactory::create($value["reason_name"]),
                    AlertChannelFactory::create($value["channel_name"], $value["channel_destination"]),
                    $customer,
                    $check
                )
            );
        }
    }

    private function alertValues()
    {
        return [
            [
                "id" => "alert-rober-1",
                "window_minutes" => 60,
                "reason_name" => AlertReasonDowntime::NAME,
                "channel_name" => AlertChannelEmail::NAME,
                "channel_destination" => "rober@example.com",
                "customer_id" => "rober",
                "check_id" => null,
            ],
            [
                "id" => "alert-rober-2",
                "window_minutes" => 15,
                "reason_name" => AlertReasonDowntime::NAME,
                "channel_name" => AlertChannelEmail::NAME,
                "channel_destination" => "rober@example.com",
                "customer_id" => "rober",
                "check_id" => "rober-check-1",
            ],
            [
                "id" => "alert-rober-3",
                "window_minutes" => 5,
                "reason_name" => AlertReasonDowntime::NAME,
                "channel_name" => AlertChannelEmail::NAME,
                "channel_destination" => "rober@example.com",
                "customer_id" => "rober",
                "check_id" => "rober-check-disabled",
            ],

            [
                "id" => "alert-laura-3",
                "window_minutes" => 10,
                "reason_name" => AlertReasonDowntime::NAME,
                "channel_name" => AlertChannelEmail::NAME,
                "channel_destination" => "rober@example.com",
                "customer_id" => "laura",
                "check_id" => null,
            ],

            [
                "id" => "alert-carol-1",
                "window_minutes" => 10,
                "reason_name" => AlertReasonDowntime::NAME,
                "channel_name" => AlertChannelEmail::NAME,
                "channel_destination" => "carol@example.com",
                "customer_id" => "carol",
                "check_id" => null,
            ],

        ];
    }

    private function findCustomer($id): Customer
    {
        return $this->customerRepository->ofId(new CustomerId($id));
    }

    private function findCheck($id = ""): ?Check
    {
        if (!$id) {
            return null;
        }

        return $this->checkRepository->ofId(new CheckId($id));
    }
}
