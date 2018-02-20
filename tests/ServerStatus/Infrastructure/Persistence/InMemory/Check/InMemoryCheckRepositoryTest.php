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

namespace ServerStatus\Infrastructure\Persistence\InMemory\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckIdDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserIdDataBuilder;

class InMemoryCheckRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnNoResultWhenRepositoryIsEmpty()
    {
        $repo = $this->createEmptyRepository();

        $this->assertNull($repo->ofId(CheckIdDataBuilder::aCheckId()->build()));
    }

    private function createEmptyRepository(): CheckRepository
    {
        return new InMemoryCheckRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnExistingCheckById()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($check);

        $this->assertSame("qwerty", $repository->ofId($id)->id()->value());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenMeasurementIsNotInRepository()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($check);
        $searchById = CheckIdDataBuilder::aCheckId()->withValue("my-random-id")->build();

        $this->assertNull($repository->ofId($searchById));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToRemoveAnExistingValueById()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this
            ->createEmptyRepository()
            ->add($check)
            ->remove($check)
        ;

        $this->assertNull($repository->ofId($id));
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Check\CheckDoesNotExistException
     */
    public function itShouldReturnExceptionWhenRemovingANonExistingMeasurement()
    {
        $id = CheckIdDataBuilder::aCheckId()->withValue("qwerty")->build();
        $check = CheckDataBuilder::aCheck()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($check);

        $new = CheckDataBuilder::aCheck()->build();
        $repository->remove($new);
    }

    /**
     * @test
     */
    public function itShouldReturnANewId()
    {
        $repository = $this->createEmptyRepository();

        $this->assertInstanceOf(CheckId::class, $repository->nextId());
    }

    /**
     * @test
     */
    public function isShouldReturnChecksForAGivenUser()
    {
        $userId = UserIdDataBuilder::aUserId()->withValue("first")->build();
        $user = UserDataBuilder::aUser()->withId($userId)->build();
        $otherUserId = UserIdDataBuilder::aUserId()->withValue("other")->build();
        $otherUser = UserDataBuilder::aUser()->withId($otherUserId)->build();
        $repository = $this->createEmptyRepository();
        $repository
            ->add(CheckDataBuilder::aCheck()->withUser($user)->build())
            ->add(CheckDataBuilder::aCheck()->withUser($otherUser)->build())
            ->add(CheckDataBuilder::aCheck()->withUser($otherUser)->build())
        ;
        $userCollection = $repository->byUser($userId);

        $this->assertEquals(1, $userCollection->count(), 'User "first" should have one Check');
        $this->assertTrue(
            $userCollection->getIterator()->current()->user()->id()->equals($userId),
            'Check should be related to User "first"'
        );
        $this->assertEquals(2, $repository->byUser($otherUserId)->count(), 'User "other" should have two Checks');
    }
}
