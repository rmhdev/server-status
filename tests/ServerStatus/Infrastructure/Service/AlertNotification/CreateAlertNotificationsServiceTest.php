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
use ServerStatus\Domain\Model\AlertNotification\AlertNotification;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;
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
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationStatusDataBuilder;
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
    private const TIME_WINDOW_MINUTES = 15;

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
            $this->alertNotificationRepository,
            new InMemoryAlertNotificationFactory()
        );
    }

    protected function tearDown()
    {
        unset($this->service);
        unset($this->measurementRepository);
        unset($this->alertNotificationRepository);
        unset($this->alertRepository);
        parent::tearDown();
    }

    private function createAlertRepository(): AlertRepository
    {
        $repository = new InMemoryAlertRepository();
        $repository
            ->add($this->createAlert(self::ENABLED_ALERT_ID, CheckStatus::CODE_ENABLED, self::TIME_WINDOW_MINUTES))
            ->add($this->createAlert(self::ENABLED_WITHOUT_CHECK_ALERT_ID, false, self::TIME_WINDOW_MINUTES))
            ->add($this->createAlert(self::DISABLED_ALERT_ID, CheckStatus::CODE_DISABLED, self::TIME_WINDOW_MINUTES))
            ->add($this->createAlert(
                self::DISABLED_CUSTOMER_ALERT_ID,
                CheckStatus::CODE_ENABLED,
                self::TIME_WINDOW_MINUTES,
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
        $alert = $this->alertRepository->ofId(new AlertId(self::ENABLED_ALERT_ID));
        $repository = new InMemoryAlertNotificationRepository();
        $repository
            ->add($this->createAlertNotification($alert, "2018-01-24", AlertNotificationStatus::SENT))
            ->add($this->createAlertNotification($alert, "2018-01-25", AlertNotificationStatus::SENDING))
            ->add($this->createAlertNotification($alert, "2018-01-26", AlertNotificationStatus::READY))
            ->add($this->createAlertNotification($alert, "2018-01-27", AlertNotificationStatus::ERROR))
        ;

        return $repository;
    }

    private function createAlertNotification(Alert $alert, $date, $code): AlertNotification
    {
        return AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withDate(new \DateTimeImmutable("{$date}T12:05:00+0200"))
            ->withStatus(
                AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
                    ->withCode($code)
                    ->build()
            )
            ->build()
        ;
    }

    private function createMeasurementRepository()
    {
        $enabledAlert = $this->alertRepository->ofId(new AlertId(self::ENABLED_ALERT_ID));
        $disabledAlert = $this->alertRepository->ofId(new AlertId(self::DISABLED_ALERT_ID));
        $disabledCustomerAlert = $this->alertRepository->ofId(new AlertId(self::DISABLED_CUSTOMER_ALERT_ID));

        $repository = new InMemoryMeasurementRepository();
        $repository
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-10", 0))
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-11", 100))
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-12", 200))
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-13", 300))
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-14", 404))
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-15", 500))

            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-10", 0))
            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-11", 100))
            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-12", 200))
            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-13", 300))
            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-14", 404))
            ->add($this->createMeasurement($disabledAlert->check(), "2018-01-15", 500))

            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-10", 0))
            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-11", 100))
            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-12", 200))
            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-13", 300))
            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-14", 404))
            ->add($this->createMeasurement($disabledCustomerAlert->check(), "2018-01-15", 500))

            // there is already a notification for this measurement.
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-24", 404))

            // the first notification for this measurement failed.
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-27", 500))

            // there are no notifications for this measurement.
            ->add($this->createMeasurement($enabledAlert->check(), "2018-01-31", 500))
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
        $collection = $this->service->create(new \DateTimeImmutable("2018-01-31T12:10:00+0200"));

        $this->assertEquals(1, $collection->count());
        $this->assertEquals(
            self::ENABLED_ALERT_ID,
            $collection->alerts()->getIterator()->current()->id()->id(),
            'The only notification is related to the enabled alert'
        );
    }

    /**
     * @test
     */
    public function itShouldNotCreateNewNotificationsForInternalErrors()
    {
        $collection = $this->service->create(new \DateTimeImmutable("2018-01-10T12:10:00+0200"));

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldCreateNewNotificationsForErrors()
    {
        $clientErrorNotifications = $this->service->create(new \DateTimeImmutable("2018-01-14T12:14:00+0200"));
        $this->assertEquals(1, $clientErrorNotifications->count(), "Should create notifications for client errors");
        $this->assertEquals(
            self::ENABLED_ALERT_ID,
            $clientErrorNotifications->getIterator()->current()->alert()->id()->id()
        );

        $serverErrorNotifications = $this->service->create(new \DateTimeImmutable("2018-01-15T12:14:00+0200"));
        $this->assertEquals(
            1,
            $serverErrorNotifications->count(),
            "Should create notifications for server errors"
        );
        $this->assertEquals(
            self::ENABLED_ALERT_ID,
            $serverErrorNotifications->getIterator()->current()->alert()->id()->id()
        );
    }

    /**
     * @test
     */
    public function itShouldNotCreateNewNotificationsForMeasurementsWithoutError()
    {
        $this->assertEquals(
            0,
            $this->service->create(new \DateTimeImmutable("2018-01-11T12:14:00+0200"))->count(),
            "No notification for informational measurements"
        );
        $this->assertEquals(
            0,
            $this->service->create(new \DateTimeImmutable("2018-01-12T12:14:00+0200"))->count(),
            "No notification successful measurements"
        );
        $this->assertEquals(
            0,
            $this->service->create(new \DateTimeImmutable("2018-01-13T12:14:00+0200"))->count(),
            "No notifications for redirects"
        );
    }

    /**
     * @test
     */
    public function itShouldNotCreateNewNotificationsIfACorrectNotificationIsAlreadyCreated()
    {
        $time = "T12:14:00+0200";
        $this->assertEquals(
            0,
            $this->service->create(new \DateTimeImmutable("2018-01-24" . $time))->count(),
            "No notification for erroneous measurements when there is already a notification SENT"
        );
        $this->assertEquals(
            0,
            $this->service->create(new \DateTimeImmutable("2018-01-25" . $time))->count(),
            "No notification for erroneous measurements when there is already a notification SENDING"
        );
        $this->assertEquals(
            0,
            $this->service->create(new \DateTimeImmutable("2018-01-26" . $time))->count(),
            "No notification for erroneous measurements when there is already a notification READY"
        );
    }

    /**
     * @test
     */
    public function itShouldCreateNewNotificationsIfAPreviousNotificationIsIncorrect()
    {
        $time = "T12:14:00+0200";
        $this->assertEquals(
            1,
            $this->service->create(new \DateTimeImmutable("2018-01-27" . $time))->count(),
            "A new notification should be created if previous one failed"
        );
    }
}
