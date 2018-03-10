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

namespace ServerStatus\Tests\Infrastructure\Ui\Console\Command;

use ServerStatus\Application\DataTransformer\Measurement\PerformanceReportsByCustomerDataTransformer;
use ServerStatus\Application\DataTransformer\Measurement\PerformanceReportsByCustomerDtoDataTransformer;
use ServerStatus\Application\Service\Check\ViewPerformanceReportsService;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryCustomerRepository;
use ServerStatus\Infrastructure\Ui\Console\Command\CustomerChecksCommand;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckUrlDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementResultDataBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CustomerChecksCommandTest extends KernelTestCase
{
    const DEFAULT_CUSTOMER_ID = "my-username";

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


    protected function setUp()
    {
        parent::setUp();
        $this->customerRepository = $this->createCustomerRepository();
        $this->checkRepository = $this->createCheckRepository();
        $this->measurementRepository = $this->createMeasurementRepository();
        $this->transformer = new PerformanceReportsByCustomerDtoDataTransformer();
    }

    protected function tearDown()
    {
        unset($this->measurementRepository);
        unset($this->checkRepository);
        unset($this->customerRepository);
        parent::tearDown();
    }

    private function createCustomerRepository(): CustomerRepository
    {
        $repo = new InMemoryCustomerRepository();
        $repo
            ->add(
                CustomerDataBuilder::aCustomer()->withId(
                    CustomerIdDataBuilder::aCustomerId()->withValue(self::DEFAULT_CUSTOMER_ID)->build()
                )->build()
            )
            ->add(CustomerDataBuilder::aCustomer()->build())
        ;

        return $repo;
    }

    private function createCheckRepository(): CheckRepository
    {
        $defaultCustomer = $this->customerRepository->ofId(
            CustomerIdDataBuilder::aCustomerId()->withValue(self::DEFAULT_CUSTOMER_ID)->build()
        );

        $repo = new InMemoryCheckRepository();
        $repo
            ->add(
                CheckDataBuilder::aCheck()->withCustomer($defaultCustomer)->withUrl(
                    CheckUrlDataBuilder::aCheckUrl()->withDomain("site.example.com")->build()
                )->build()
            )
            ->add(
                CheckDataBuilder::aCheck()->withCustomer($defaultCustomer)->withUrl(
                    CheckUrlDataBuilder::aCheckUrl()->withDomain("example.io")->build()
                )->build()
            )
            ->add(
                CheckDataBuilder::aCheck()->withUrl(
                    CheckUrlDataBuilder::aCheckUrl()->withDomain("site.example.com")->build()
                )->build()
            )
        ;

        return $repo;
    }

    private function createMeasurementRepository(): MeasurementRepository
    {
        $repository = new InMemoryMeasurementRepository();
        $customer = $this->customerRepository->ofId(
            CustomerIdDataBuilder::aCustomerId()->withValue(self::DEFAULT_CUSTOMER_ID)->build()
        );
        $date = new \DateTimeImmutable("2018-03-01T00:00:00+0200");
        $codes = [
            200, 200, 200, 200, 100, 404, 100, 500
        ];
        foreach ($this->checkRepository->byCustomer($customer->id()) as $check) {
            foreach ($codes as $i => $code) {
                $measurement = MeasurementDataBuilder::aMeasurement()
                    ->withCheck($check)
                    ->withDate($date->modify(sprintf("+%d minutes", 30 * $i)))
                    ->withResult(
                        MeasurementResultDataBuilder::aMeasurementResult()
                            ->withDuration(100 * $i + $code)
                            ->withStatus($code)
                            ->build()
                    )->build();
                $repository->add($measurement);
            }
        }

        return $repository;
    }

    private function findCommand(): CustomerChecksCommand
    {
        $service = new ViewPerformanceReportsService(
            $this->customerRepository,
            $this->checkRepository,
            $this->measurementRepository,
            $this->transformer
        );

        return new CustomerChecksCommand($service);
    }

    /**
     * @test
     */
    public function itShouldReturnMessageWhenCustomerIsNotFound()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($this->findCommand());
        $command = $application->find('server-status:checks');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'id'       => "unknown"
        ]);
        $output = $commandTester->getDisplay();

        $this->assertContains("Customer: not found", $output);
    }

    /**
     * @test
     */
    public function itShouldReturnAPerformanceReportForEveryCheck()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($this->findCommand());
        $command = $application->find('server-status:checks');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'id'       => self::DEFAULT_CUSTOMER_ID
        ]);
        $output = $commandTester->getDisplay();

        $this->assertContains("Checks: 2", $output);
    }
}
