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

namespace ServerStatus\Domain\Model\AlertNotification\Log;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationCollection;
use ServerStatus\Domain\Model\AlertNotification\InvalidAlertNotificationException;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;

final class AlertNotificationsLog
{
    /**
     * @var Alert
     */
    private $alert;

    /**
     * @var DateRange
     */
    private $dateRange;

    /**
     * @var AlertNotificationCollection
     */
    private $alertNotificationCollection;


    public function __construct(Alert $alert, DateRange $dateRange, ?AlertNotificationCollection $collection)
    {
        $collection = $this->processAlertNotificationCollection($collection);
        $this->assertSameAlert($collection, $alert);
        $this->alert = $alert;
        $this->dateRange = $dateRange;
        $this->alertNotificationCollection = $collection;
    }

    private function processAlertNotificationCollection(
        ?AlertNotificationCollection $collection
    ): AlertNotificationCollection {
        $collection = $collection ? $collection : new AlertNotificationCollection();

        return $collection;
    }

    private function assertSameAlert(AlertNotificationCollection $collection, Alert $expectedAlert)
    {
        $alerts = $collection->alerts();
        if (1 < $alerts->count()) {
            throw new InvalidAlertNotificationException(
                sprintf('The collection only accepts items from a single alert, %d alerts found', $alerts->count())
            );
        }
        if (1 === $alerts->count()) {
            $alertFound = $alerts->getIterator()->current();
            if (!$expectedAlert->id()->equals($alertFound->id())) {
                throw new InvalidAlertNotificationException(sprintf(
                    'The collection only accepts items from alert "%s", notification from alert "%s" received',
                    $expectedAlert->id(),
                    $alertFound->id()
                ));
            }
        }
    }

    public function alert(): Alert
    {
        return $this->alert;
    }

    public function dateRange(): DateRange
    {
        return $this->dateRange;
    }

    public function alertNotificationCollection(): AlertNotificationCollection
    {
        return $this->alertNotificationCollection;
    }
}
