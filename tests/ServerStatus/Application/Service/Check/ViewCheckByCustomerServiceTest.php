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

namespace ServerStatus\Tests\Application\Service\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\DataTransformer\Check\CheckDtoDataTransformer;
use ServerStatus\Application\DataTransformer\Check\CheckDataTransformer;
use ServerStatus\Application\Service\Check\ViewCheckByCustomerRequest;
use ServerStatus\Application\Service\Check\ViewCheckByCustomerService;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryCustomerRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckNameDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class ViewCheckByCustomerServiceTest extends TestCase
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var CheckRepository
     */
    private $checkRepository;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;

    /**
     * @var CheckDataTransformer
     */
    private $checkDataTransformer;

    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var CheckName
     */
    private $checkName;


    protected function setUp()
    {
        parent::setUp();

        $id = CustomerIdDataBuilder::aCustomerId()->withValue("qwerty")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($id)->build();

        $customerRepository = new InMemoryCustomerRepository();
        $customerRepository->add($customer);

        $checkName = CheckNameDataBuilder::aCheckName()->withName("My check")->withSlug("my-check")->build();
        $checkRepo = new InMemoryCheckRepository();
        $checkRepo
            ->add(CheckDataBuilder::aCheck()->withName($checkName)->withCustomer($customer)->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($customer)->build())
            ->add(CheckDataBuilder::aCheck()->build())
        ;

        // fake measurements for $customerId's checks:
        $measurementRepo = new InMemoryMeasurementRepository();
        foreach ($checkRepo->byCustomer($id) as $check) {
            $measurementRepo->add($this->createMeasurements($check));
        }

        $this->customerId = $id;
        $this->checkName = $checkName;
        $this->customerRepository = $customerRepository;
        $this->checkRepository = $checkRepo;
        $this->measurementRepository = $measurementRepo;
        $this->checkDataTransformer = new CheckDtoDataTransformer();
    }

    private function createMeasurements(Check $check)
    {
        return [
            MeasurementDataBuilder::aMeasurement()
                ->withCheck($check)
                ->withDate(new \DateTime("2018-02-03T00:00:00+0200"))
                ->build(),
            MeasurementDataBuilder::aMeasurement()
                ->withCheck($check)
                ->withDate(new \DateTime("2018-02-03T01:00:00+0200"))
                ->build(),
            MeasurementDataBuilder::aMeasurement()
                ->withCheck($check)
                ->withDate(new \DateTime("2018-02-03T02:00:00+0200"))
                ->build()
        ];
    }

    protected function tearDown()
    {
        unset($this->checkDataTransformer);
        unset($this->measurementRepository);
        unset($this->checkRepository);
        unset($this->customerRepository);
        unset($this->checkName);
        unset($this->customerId);

        parent::tearDown();
    }

    private function createService(): ViewCheckByCustomerService
    {
        return new ViewCheckByCustomerService(
            $this->customerRepository,
            $this->checkRepository,
            $this->measurementRepository,
            $this->checkDataTransformer
        );
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException
     */
    public function itShouldThrowExceptionWhenCustomerIsNotFound()
    {
        $this->createService()->execute(
            new ViewCheckByCustomerRequest(
                CustomerIdDataBuilder::aCustomerId()->withValue("should-not-exist")->build(),
                $this->checkName
            )
        );
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Check\CheckDoesNotExistException
     */
    public function itShouldThrowExceptionWhenCheckIsNotFound()
    {
        $this->createService()->execute(
            new ViewCheckByCustomerRequest(
                $this->customerId,
                CheckNameDataBuilder::aCheckName()->withSlug("should-not-exist")->build()
            )
        );
    }

    /**
     * @test
     */
    public function itShouldReturnCustomerWhenFound()
    {
        $data = $this->createService()->execute(
            new ViewCheckByCustomerRequest($this->customerId, $this->checkName)
        );

        $this->assertEquals($this->customerId->id(), $data["customer"]["id"]);
    }

    /**
     * @test
     */
    public function itShouldReturnCheck()
    {
        $data = $this->createService()->execute(
            new ViewCheckByCustomerRequest($this->customerId, $this->checkName)
        );

        $this->assertEquals("My check", $data["check"]["name"]);
    }

    /**
     * @test
     */
    public function itShouldReturnAListOfAverages()
    {
        $data = $this->createService()->execute(
            new ViewCheckByCustomerRequest($this->customerId, $this->checkName)
        );

        $this->assertGreaterThan(0, sizeof($data["measure_summary"]["averages"]));
    }
}
