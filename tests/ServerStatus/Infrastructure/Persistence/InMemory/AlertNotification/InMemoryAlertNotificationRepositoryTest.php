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
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationDataBuilder;
use ServerStatus\Tests\Domain\Model\AlertNotification\AlertNotificationIdDataBuilder;

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
}
