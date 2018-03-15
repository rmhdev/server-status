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

namespace ServerStatus\Tests\Infrastructure\Service\AlertNotification;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckStatus;
use ServerStatus\Domain\Model\Customer\CustomerStatus;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Infrastructure\Domain\Model\AlertNotification\InMemoryAlertNotificationFactory;
use ServerStatus\Infrastructure\Persistence\InMemory\Alert\InMemoryAlertRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\AlertNotification\InMemoryAlertNotificationRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Infrastructure\Service\AlertNotification\CreateAlertNotificationsService;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\AlertIdDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\AlertTimeWindowDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementResultDataBuilder;

class CreateAlertNotificationsServiceTest extends TestCase
{
    private const ENABLED_ALERT_ID = "my-enabled-alert";
    private const ENABLED_WITHOUT_CHECK_ALERT_ID = "without-check-enabled-alert";
    private const DISABLED_ALERT_ID = "my-disabled-alert";
    private const DISABLED_CUSTOMER_ALERT_ID = "my-disabled-customer-alert";

    /**
     * @var AlertRepository
     */
    private $alertRepository;

    /**
     * @var AlertNotificationRepository
     */
    private $alertNotificationRepository;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;

    /**
     * @var CreateAlertNotificationsService
     */
    private $service;


    protected function setUp()
    {
        parent::setUp();
        $this->alertRepository = $this->createAlertRepository();
        $this->alertNotificationRepository = $this->createAlertNotificationRepository();
        $this->measurementRepository = $this->createMeasurementRepository();
        $this->service = new CreateAlertNotificationsService(
            $this->alertRepository,
            $this->measurementRepository,
            new InMemoryAlertNotificationFactory()
        );
    }

    protected function tearDown()
    {
        unset($this->measurementRepository);
        unset($this->alertNotificationRepository);
        unset($this->alertRepository);
        parent::tearDown();
    }

    private function createAlertRepository(): AlertRepository
    {
        $checkStatusEnabled = CheckStatus::CODE_ENABLED;
        $repository = new InMemoryAlertRepository();
        $repository
            ->add($this->createAlert(self::ENABLED_ALERT_ID, $checkStatusEnabled, 15))
            ->add($this->createAlert(self::ENABLED_WITHOUT_CHECK_ALERT_ID, false, 15))
            ->add($this->createAlert(self::DISABLED_ALERT_ID, $checkStatusEnabled, 15))
            ->add($this->createAlert(
                self::DISABLED_CUSTOMER_ALERT_ID,
                $checkStatusEnabled,
                15,
                CustomerStatus::CODE_DISABLED
            ))
        ;

        return $repository;
    }

    private function createAlert(
        $alertId,
        $checkStatus = "",
        $timeWindowMinutes = 5,
        $customerStatus = CustomerStatus::CODE_ENABLED
    ): Alert {
        $customer = CustomerDataBuilder::aCustomer()->withId($alertId)->withStatus($customerStatus)->build();

        return AlertDataBuilder::anAlert()
            ->withId(AlertIdDataBuilder::anAlertId()->withValue($alertId)->build())
            ->withCustomer($customer)
            ->withCheck(
                $checkStatus ?
                CheckDataBuilder::aCheck()->withCustomer($customer)->withStatus($checkStatus)->build() :
                null
            )->withWindow(
                AlertTimeWindowDataBuilder::anAlertTimeWindow()->withValue($timeWindowMinutes)->build()
            )->build();
    }

    private function createAlertNotificationRepository(): AlertNotificationRepository
    {
        $repository = new InMemoryAlertNotificationRepository();

        return $repository;
    }

    private function createMeasurementRepository()
    {
        $enabledAlert = $this->alertRepository->ofId(new AlertId(self::ENABLED_ALERT_ID));
        $disabledAlert = $this->alertRepository->ofId(new AlertId(self::DISABLED_ALERT_ID));
        $disabledCustomerAlert = $this->alertRepository->ofId(new AlertId(self::DISABLED_CUSTOMER_ALERT_ID));

        $repository = new InMemoryMeasurementRepository();
        $repository
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-01", 200))
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-02", 404))
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-03", 500))

            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-01", 200))
            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-02", 404))
            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-03", 500))

            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-01", 200))
            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-02", 404))
            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-03", 500))
        ;

        return $repository;
    }

    private function createMeasurement(Check $check, string $date, int $statusCode): Measurement
    {
        return MeasurementDataBuilder::aMeasurement()
            ->withCheck($check)
            ->withDate(new \DateTimeImmutable("{$date}T12:00:00+0200"))
            ->withResult(MeasurementResultDataBuilder::aMeasurementResult()->withStatus($statusCode)->build())
            ->build();
    }

    /**
     * @test
     */
    public function itShouldCreateANewNotificationForAnActiveAlert()
    {
        $collection = $this->service->create(new \DateTimeImmutable("2018-01-01T12:00:00+0200"));

        $this->assertEquals(1, $collection->count());
    }
}
