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
use ServerStatus\Tests\Domain\Model\Customer\CustomerEmailDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementResultDataBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CustomerChecksCommandTest extends KernelTestCase
{
    const DEFAULT_CUSTOMER_EMAIL = "test@example.com";

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


    protected function setUp()
    {
        parent::setUp();
        $this->customerRepository = $this->createCustomerRepository();
        $this->checkRepository = $this->createCheckRepository();
        $this->measurementRepository = $this->createMeasurementRepository();
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
                    CustomerIdDataBuilder::aCustomerId()->withValue(self::DEFAULT_CUSTOMER_EMAIL)->build()
                )->withEmail(
                    CustomerEmailDataBuilder::aCustomerEmail()->withValue(self::DEFAULT_CUSTOMER_EMAIL)->build()
                )->build()
            )
            ->add(
                CustomerDataBuilder::aCustomer()->build()
            )
        ;

        return $repo;
    }

    private function createCheckRepository(): CheckRepository
    {
        $defaultCustomer = $this->customerRepository->ofEmail(
            CustomerEmailDataBuilder::aCustomerEmail()->withValue(self::DEFAULT_CUSTOMER_EMAIL)->build()
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
        $customer = $this->customerRepository->ofEmail(
            CustomerEmailDataBuilder::aCustomerEmail()->withValue(self::DEFAULT_CUSTOMER_EMAIL)->build()
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

    /**
     * @test
     */
    public function itShouldReturnReportForLast24HoursByDefault()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($this->findCommand());
        $command = $application->find('server-status:checks');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'email'    => self::DEFAULT_CUSTOMER_EMAIL
        ]);
        $output = $commandTester->getDisplay();

        $this->assertContains("Date range: last_24_hours", $output);
    }

    private function findCommand(): CustomerChecksCommand
    {
        return new CustomerChecksCommand(
            $this->customerRepository,
            $this->checkRepository,
            $this->measurementRepository
        );
    }
}
