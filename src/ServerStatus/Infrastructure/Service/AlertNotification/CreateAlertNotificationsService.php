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

namespace ServerStatus\Infrastructure\Service\AlertNotification;

use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationCollection;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationFactory;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationId;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

final class CreateAlertNotificationsService
{
    /**
     * @var AlertRepository
     */
    private $alertRepository;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;

    /**
     * @var AlertNotificationRepository
     */
    private $alertNotificationRepository;

    /**
     * @var AlertNotificationFactory
     */
    private $factory;


    public function __construct(
        AlertRepository $alertRepository,
        MeasurementRepository $measurementRepository,
        AlertNotificationRepository $alertNotificationRepository,
        AlertNotificationFactory $factory
    ) {
        $this->alertRepository = $alertRepository;
        $this->measurementRepository = $measurementRepository;
        $this->alertNotificationRepository = $alertNotificationRepository;
        $this->factory = $factory;
    }

    public function create(\DateTimeInterface $dateTime): AlertNotificationCollection
    {
        $alertNotifications = [];
        foreach ($this->alertRepository->enabled() as $alert) {
            //$notifications = $this->alertNotificationRepository
            //    ->byAlert($alert, $alert->timeWindow()->dateRange($dateTime));

            $measurements = $this->measurementRepository->findErrors($alert, $dateTime);
            if ($measurements->count() > 0) {
                $alertNotifications[] = $this->factory->build(
                    new AlertNotificationId(),
                    $alert,
                    $dateTime,
                    new AlertNotificationStatus(AlertNotificationStatus::READY)
                );
            }
        }


        return new AlertNotificationCollection($alertNotifications);
    }
}
