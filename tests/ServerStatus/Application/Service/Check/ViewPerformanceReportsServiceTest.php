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
use ServerStatus\Application\DataTransformer\Measurement\PerformanceReportsByCustomerDataTransformer;
use ServerStatus\Application\DataTransformer\Measurement\PerformanceReportsByCustomerDtoDataTransformer;
use ServerStatus\Application\Service\Check\ViewChecksByCustomerRequest;
use ServerStatus\Application\Service\Check\ViewPerformanceReportsService;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryCustomerRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class ViewPerformanceReportsServiceTest extends TestCase
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
     * @var PerformanceReportsByCustomerDataTransformer
     */
    private $transformer;

    /**
     * @var CustomerId
     */
    private $customerId;


    protected function setUp()
    {
        parent::setUp();

        $id = CustomerIdDataBuilder::aCustomerId()->withValue("qwerty")->build();
        $customer = CustomerDataBuilder::aCustomer()->withId($id)->build();

        $customerRepository = new InMemoryCustomerRepository();
        $customerRepository->add($customer);

        $checkRepo = new InMemoryCheckRepository();
        $checkRepo
            ->add(CheckDataBuilder::aCheck()->withCustomer($customer)->withName("first")->build())
            ->add(CheckDataBuilder::aCheck()->withCustomer($customer)->withName("second")->build())
            ->add(CheckDataBuilder::aCheck()->withName("third")->build())
        ;

        // fake measurements for $customerId's checks:
        $measurementRepo = new InMemoryMeasurementRepository();
        foreach ($checkRepo->byCustomer($id) as $check) {
            $measurementRepo->add($this->createMeasurements($check));
        }

        $this->customerId = $id;
        $this->customerRepository = $customerRepository;
        $this->checkRepository = $checkRepo;
        $this->measurementRepository = $measurementRepo;
        $this->transformer = new PerformanceReportsByCustomerDtoDataTransformer();
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
        unset($this->transformer);
        unset($this->measurementRepository);
        unset($this->checkRepository);
        unset($this->customerRepository);
        unset($this->customerId);

        parent::tearDown();
    }

    private function createService(): ViewPerformanceReportsService
    {
        return new ViewPerformanceReportsService(
            $this->customerRepository,
            $this->checkRepository,
            $this->measurementRepository,
            $this->transformer
        );
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException
     */
    public function itShouldThrowExceptionWhenCustomerIsNotFound()
    {
        $this->createService()->execute(
            new ViewChecksByCustomerRequest(
                CustomerIdDataBuilder::aCustomerId()->withValue("should-not-exist")->build()
            )
        );
    }

    /**
     * @test
     */
    public function itShouldReturnCustomerWhenFound()
    {
        $data = $this->createService()->execute(
            new ViewChecksByCustomerRequest($this->customerId)
        );

        $this->assertEquals($this->customerId->id(), $data["customer"]["id"]);
    }

    /**
     * @test
     */
    public function itShouldReturnOnePerformanceReportForEveryCheck()
    {
        $data = $this->createService()->execute(
            new ViewChecksByCustomerRequest(
                $this->customerId,
                new \DateTime("2018-02-03T00:00:00+0200"),
                DateRangeDay::NAME
            )
        );

        $this->assertEquals(2, sizeof($data["performance_reports"]));
    }
}
