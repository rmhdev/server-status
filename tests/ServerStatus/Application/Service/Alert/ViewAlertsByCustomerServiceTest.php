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
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Alert\InMemoryAlertRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryCustomerRepository;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
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

        $alertRepository = new InMemoryAlertRepository();
        $alertRepository
            ->add(AlertDataBuilder::anAlert()->withCustomer($customer)->build())
            ->add(AlertDataBuilder::anAlert()->withCustomer($customer)->build())
            ->add(AlertDataBuilder::anAlert()->withCustomer($customer)->build())
        ;

        $this->customerId = $id;
        $this->customerRepository = $customerRepository;
        $this->alertRepository = $alertRepository;
        $this->transformer = new CustomerAlertsDtoDataTransformer();
    }

    protected function tearDown()
    {
        unset($this->transformer);
        unset($this->alertRepository);
        unset($this->customerRepository);
        unset($this->customerId);

        parent::tearDown();
    }

    private function createService(): ViewAlertsByCustomerService
    {
        return new ViewAlertsByCustomerService(
            $this->customerRepository,
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
}
