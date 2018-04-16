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

namespace ServerStatus\Infrastructure\Persistence\InMemory\AlertNotification;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationId;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationStatus;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Tests\Domain\Model\Alert\AlertDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationIdDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationStatusDataBuilder;

class InMemoryAlertNotificationRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnNoResultWhenRepositoryIsEmpty()
    {
        $repo = $this->createEmptyRepository();

        $this->assertNull($repo->ofId(AlertNotificationIdDataBuilder::anAlertNotificationId()->build()));
    }

    private function createEmptyRepository(): AlertNotificationRepository
    {
        return new InMemoryAlertNotificationRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnExistingAlertNotificationById()
    {
        $id = AlertNotificationIdDataBuilder::anAlertNotificationId()->withValue("qwerty")->build();
        $notification = AlertNotificationDataBuilder::anAlertNotification()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($notification);

        $this->assertSame("qwerty", $repository->ofId($id)->id()->id());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenAlertNotificationIsNotInRepository()
    {
        $id = AlertNotificationIdDataBuilder::anAlertNotificationId()->withValue("qwerty")->build();
        $notification = AlertNotificationDataBuilder::anAlertNotification()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($notification);
        $searchById = AlertNotificationIdDataBuilder::anAlertNotificationId()->withValue("my-random-id")->build();

        $this->assertNull($repository->ofId($searchById));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToRemoveAnExistingValueById()
    {
        $id = AlertNotificationIdDataBuilder::anAlertNotificationId()->withValue("qwerty")->build();
        $notification = AlertNotificationDataBuilder::anAlertNotification()->withId($id)->build();
        $repository = $this
            ->createEmptyRepository()
            ->add($notification)
            ->remove($notification)
        ;

        $this->assertNull($repository->ofId($id));
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\AlertNotification\AlertNotificationDoesNotExistException
     */
    public function itShouldThrowExceptionWhenRemovingANonExistingAlertNotification()
    {
        $id = AlertNotificationIdDataBuilder::anAlertNotificationId()->withValue("qwerty")->build();
        $notification = AlertNotificationDataBuilder::anAlertNotification()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($notification);

        $new = AlertNotificationDataBuilder::anAlertNotification()->build();
        $repository->remove($new);
    }

    /**
     * @test
     */
    public function itShouldReturnANewId()
    {
        $repository = $this->createEmptyRepository();

        $this->assertInstanceOf(AlertNotificationId::class, $repository->nextId());
    }

    /**
     * @test
     */
    public function itShouldFilterByAlertUsingStrictlyTheDateRange()
    {
        $alert = AlertDataBuilder::anAlert()->build();
        $notificationA = AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withDate(new \DateTimeImmutable("2018-03-03T11:59:59+0200"))
            ->build();
        $notificationB = AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withDate(new \DateTimeImmutable("2018-03-04T11:59:59+0200"))
            ->build();
        $notificationC = AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withDate(new \DateTimeImmutable("2018-03-03T12:00:00+0200"))
            ->build();
        $notificationD = AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withDate(new \DateTimeImmutable("2018-03-04T12:00:00+0200"))
            ->build();
        $repository = $this->createEmptyRepository()
            ->add($notificationA)->add($notificationB)->add($notificationC)->add($notificationD);
        $dateRange = DateRangeFactory::create(
            DateRangeLast24Hours::NAME,
            new \DateTimeImmutable("2018-03-04T12:00:00+0200")
        );
        $collection = $repository->byAlert($alert->id(), $dateRange);

        $this->assertEquals(2, $collection->count());
    }

    /**
     * @test
     */
    public function itShouldFilterByDifferentStatusCodesIfDefined()
    {
        $date = new \DateTimeImmutable("2018-03-03T12:00:00+0200");

        $alert = AlertDataBuilder::anAlert()->build();
        $notificationA = AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withStatus(
                AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
                ->withCode(AlertNotificationStatus::SENT)
                ->build()
            )
            ->withDate($date)
            ->build();
        $notificationB = AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withStatus(
                AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
                    ->withCode(AlertNotificationStatus::ERROR)
                    ->build()
            )
            ->withDate($date->modify("+2 hours"))
            ->build();
        $notificationC = AlertNotificationDataBuilder::anAlertNotification()
            ->withAlert($alert)
            ->withStatus(
                AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
                    ->withCode(AlertNotificationStatus::SENDING)
                    ->build()
            )
            ->withDate($date->modify("+4 hours"))
            ->build();
        $repository = $this->createEmptyRepository()
            ->add($notificationA)->add($notificationB)->add($notificationC);

        $dateRange = DateRangeFactory::create(
            DateRangeLast24Hours::NAME,
            new \DateTimeImmutable("2018-03-04T12:00:00+0200")
        );

        $this->assertEquals(
            1,
            $repository->byAlert($alert->id(), $dateRange, AlertNotificationStatus::SENT)->count(),
            'Return all notifications with success codes'
        );
        $this->assertEquals(
            2,
            $repository->byAlert($alert->id(), $dateRange, AlertNotificationStatus::successCodes())->count(),
            'Return all notifications with success codes'
        );

        $errorStatus = AlertNotificationStatusDataBuilder::anAlertNotificationStatus()
            ->withCode(AlertNotificationStatus::ERROR)
            ->build();
        $this->assertEquals(
            1,
            $repository->byAlert($alert->id(), $dateRange, [$errorStatus])->count(),
            'It should accept AlertNotificationStatus objects as filters'
        );
    }
}
