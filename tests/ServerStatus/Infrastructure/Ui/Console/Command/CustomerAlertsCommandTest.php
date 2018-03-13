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

use ServerStatus\Application\DataTransformer\Alert\CustomerAlertsDataTransformer;
use ServerStatus\Application\DataTransformer\Alert\CustomerAlertsDtoDataTransformer;
use ServerStatus\Application\Service\Alert\ViewAlertsByCustomerService;
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Alert\InMemoryAlertRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\AlertNotification\InMemoryAlertNotificationRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryCustomerRepository;
use ServerStatus\Infrastructure\Ui\Console\Command\CustomerAlertsCommand;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\AlertIdDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CustomerAlertsCommandTest extends KernelTestCase
{
    const DEFAULT_CUSTOMER_ID = "my-username";
    const DEFAULT_ALERT_ID = "my-username-alert";

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var AlertRepository
     */
    private $alertRepository;

    /**
     * @var AlertNotificationRepository
     */
    private $alertNotificationRepository;

    /**
     * @var CustomerAlertsDataTransformer
     */
    private $transformer;


    protected function setUp()
    {
        parent::setUp();
        $this->customerRepository = $this->createCustomerRepository();
        $this->alertRepository = $this->createAlertRepository();
        $this->alertNotificationRepository = $this->createAlertNotificationRepository();
        $this->transformer = new CustomerAlertsDtoDataTransformer();
    }

    protected function tearDown()
    {
        unset($this->transformer);
        unset($this->alertNotificationRepository);
        unset($this->alertRepository);
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

    private function createAlertRepository(): AlertRepository
    {
        $defaultCustomer = $this->customerRepository->ofId(
            CustomerIdDataBuilder::aCustomerId()->withValue(self::DEFAULT_CUSTOMER_ID)->build()
        );

        $repo = new InMemoryAlertRepository();
        $repo
            ->add(
                AlertDataBuilder::anAlert()
                    ->withId(AlertIdDataBuilder::anAlertId()->withValue(self::DEFAULT_ALERT_ID)->build())
                    ->withCustomer($defaultCustomer)
                    ->build()
            )->add(
                AlertDataBuilder::anAlert()->withCustomer($defaultCustomer)->withCheck(
                    CheckDataBuilder::aCheck()->withCustomer($defaultCustomer)->build()
                )->build()
            )
        ;

        return $repo;
    }

    private function createAlertNotificationRepository(): AlertNotificationRepository
    {
        $defaultAlert = $this->alertRepository->ofId(
            AlertIdDataBuilder::anAlertId()->withValue(self::DEFAULT_ALERT_ID)->build()
        );

        $repo = new InMemoryAlertNotificationRepository();
        $repo
            ->add(
                AlertNotificationDataBuilder::anAlertNotification()
                    ->withAlert($defaultAlert)
                    ->withDate(new \DateTimeImmutable("2018-03-03T12:00:00+0200"))
                    ->build()
            )->add(
                AlertNotificationDataBuilder::anAlertNotification()
                    ->withAlert($defaultAlert)
                    ->withDate(new \DateTimeImmutable("2018-03-03T16:00:00+0200"))
                    ->build()
            )
        ;

        return $repo;
    }

    private function findCommand(): CustomerAlertsCommand
    {
        $service = new ViewAlertsByCustomerService(
            $this->customerRepository,
            $this->alertRepository,
            $this->alertNotificationRepository,
            $this->transformer
        );

        return new CustomerAlertsCommand($service);
    }

    /**
     * @test
     */
    public function itShouldReturnMessageWhenCustomerIsNotFound()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($this->findCommand());
        $command = $application->find('server-status:alerts');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            'id'       => "unknown"
        ]);
        $output = $commandTester->getDisplay();

        $this->assertContains("Customer: not found", $output);
    }
}
