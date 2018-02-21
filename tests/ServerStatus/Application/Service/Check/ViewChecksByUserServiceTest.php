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

namespace ServerStatus\Tests\Application\Service\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\DataTransformer\User\UserChecksDataTransformer;
use ServerStatus\Application\DataTransformer\User\UserChecksDtoDataTransformer;
use ServerStatus\Application\Service\Check\ViewChecksByUserRequest;
use ServerStatus\Application\Service\Check\ViewChecksByUserService;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureDaySummary;
use ServerStatus\Domain\Model\User\UserRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryUserRepository;
use ServerStatus\ServerStatus\Domain\Model\User\User;
use ServerStatus\Tests\Domain\Model\User\UserDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserIdDataBuilder;

class ViewChecksByUserServiceTest extends TestCase
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserChecksDataTransformer
     */
    private $userTransformer;

    /**
     * @var User
     */
    private $user;


    protected function setUp()
    {
        parent::setUp();

        $userId = UserIdDataBuilder::aUserId()->withValue("qwerty")->build();
        $user = UserDataBuilder::aUser()->withId($userId)->build();

        $userRepo = new InMemoryUserRepository();
        $userRepo->add($user);

        $this->user = $user;
        $this->userRepository = $userRepo;
        $this->userTransformer = new UserChecksDtoDataTransformer();
    }

    protected function tearDown()
    {
        unset($this->userTransformer);
        unset($this->userRepository);
        unset($this->user);

        parent::tearDown();
    }

    private function user(): User
    {
        return $this->user;
    }

    /**
     * @test
     */
    public function isShouldReturnEmptyListWhenNoRequestIsGiven()
    {
        $service = new ViewChecksByUserService($this->userRepository, $this->userTransformer);

        $this->assertEquals([], $service->execute());
    }

    /**
     * @test
     */
    public function isShouldReturnUserWhenFound()
    {
        $request = new ViewChecksByUserRequest(
            $this->user->id(),
            new \DateTimeImmutable("2018-02-02T15:24:10+0200"),
            MeasureDaySummary::NAME
        );
        $service = new ViewChecksByUserService($this->userRepository, $this->userTransformer);
        $data = $service->execute($request);

        $this->assertEquals($this->user()->id()->value(), $data["user"]["id"]);
    }
}
