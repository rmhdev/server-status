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

namespace ServerStatus\Tests\Application\Service\Alert;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\DataTransformer\Alert\CustomerAlertsDataTransformer;
use ServerStatus\Application\DataTransformer\Alert\CustomerAlertsDtoDataTransformer;
use ServerStatus\Application\Service\Alert\ViewAlertsByCustomerRequest;
use ServerStatus\Application\Service\Alert\ViewAlertsByCustomerService;
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Alert\InMemoryAlertRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\AlertNotification\InMemoryAlertNotificationRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryCustomerRepository;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerIdDataBuilder;

class ViewAlertsByCustomerServiceTest extends TestCase
{
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

        $alert = AlertDataBuilder::anAlert()->withCustomer($customer)->build();
        $alertRepository = new InMemoryAlertRepository();
        $alertRepository
            ->add($alert)
            ->add(AlertDataBuilder::anAlert()->withCustomer($customer)->build())
            ->add(AlertDataBuilder::anAlert()->build())
        ;

        $alertNotificationRepository = new InMemoryAlertNotificationRepository();
        $alertNotificationRepository
            ->add(
                AlertNotificationDataBuilder::anAlertNotification()
                    ->withAlert($alert)
                    ->withDate(new \DateTimeImmutable("2018-03-03T12:00:00+0200"))
                    ->build()
            )->add(
                AlertNotificationDataBuilder::anAlertNotification()
                    ->withAlert($alert)
                    ->withDate(new \DateTimeImmutable("2018-03-03T18:30:00+0200"))
                    ->build()
            )
        ;

        $this->customerId = $id;
        $this->customerRepository = $customerRepository;
        $this->alertRepository = $alertRepository;
        $this->alertNotificationRepository = $alertNotificationRepository;
        $this->transformer = new CustomerAlertsDtoDataTransformer();
    }

    protected function tearDown()
    {
        unset($this->transformer);
        unset($this->alertNotificationRepository);
        unset($this->alertRepository);
        unset($this->customerRepository);
        unset($this->customerId);

        parent::tearDown();
    }

    private function createService(): ViewAlertsByCustomerService
    {
        return new ViewAlertsByCustomerService(
            $this->customerRepository,
            $this->alertRepository,
            $this->alertNotificationRepository,
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
            new ViewAlertsByCustomerRequest(
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
            new ViewAlertsByCustomerRequest($this->customerId)
        );

        $this->assertEquals($this->customerId->id(), $data["customer"]["id"]);
    }

    /**
     * @test
     */
    public function itShouldReturnAListOfAlerts()
    {
        $data = $this->createService()->execute(
            new ViewAlertsByCustomerRequest($this->customerId)
        );

        $this->assertEquals(2, sizeof($data["alerts"]));
    }

    /**
     * @test
     */
    public function itShouldReturnAListOfNotificationsForEveryAlert()
    {
        $data = $this->createService()->execute(
            new ViewAlertsByCustomerRequest(
                $this->customerId,
                new \DateTimeImmutable("2018-03-03T18:30:00+0200"),
                DateRangeDay::NAME
            )
        );

        $this->assertEquals(2, sizeof($data["alerts"][0]["notifications"]));
    }
}
