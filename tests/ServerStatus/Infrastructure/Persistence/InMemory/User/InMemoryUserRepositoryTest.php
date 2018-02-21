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

namespace ServerStatus\Tests\Infrastructure\Persistence\InMemory\User;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\User\UserId;
use ServerStatus\Domain\Model\User\UserRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryUserRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementIdDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserIdDataBuilder;

class InMemoryUserRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function isShouldReturnNoResultWhenRepositoryIsEmpty()
    {
        $repo = $this->createEmptyRepository();

        $this->assertNull($repo->ofId(UserIdDataBuilder::aUserId()->build()));
    }

    private function createEmptyRepository(): UserRepository
    {
        return new InMemoryUserRepository();
    }

    /**
     * @test
     */
    public function itShouldReturnExistingUserById()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("qwerty")->build();
        $user = UserDataBuilder::aUser()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($user);

        $this->assertSame("qwerty", $repository->ofId($id)->id()->value());
    }

    /**
     * @test
     */
    public function itShouldReturnNoResultWhenUserIsNotInRepository()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("qwerty")->build();
        $user = UserDataBuilder::aUser()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($user);
        $searchById = UserIdDataBuilder::aUserId()->withValue("other-id")->build();

        $this->assertNull($repository->ofId($searchById));
    }

    /**
     * @test
     */
    public function itShouldBeAbleToRemoveAnExistingValueById()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("qwerty")->build();
        $user = UserDataBuilder::aUser()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($user);
        $repository->remove($user);

        $this->assertNull($repository->ofId($id));
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\User\UserDoesNotExistException
     */
    public function itShouldReturnExceptionWhenRemovingANonExistingUser()
    {
        $id = UserIdDataBuilder::aUserId()->withValue("qwerty")->build();
        $user = UserDataBuilder::aUser()->withId($id)->build();
        $repository = $this->createEmptyRepository();
        $repository->add($user);

        $newUser = UserDataBuilder::aUser()->build();
        $repository->remove($newUser);
    }

    /**
     * @test
     */
    public function itShouldReturnANewId()
    {
        $repository = $this->createEmptyRepository();

        $this->assertInstanceOf(UserId::class, $repository->nextId());
    }
}
