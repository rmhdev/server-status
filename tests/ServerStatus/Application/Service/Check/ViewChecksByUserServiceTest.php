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
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryUserRepository;
use ServerStatus\Tests\Domain\Model\User\UserDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserIdDataBuilder;

class ViewChecksByUserServiceTest extends TestCase
{
    private $userRepository;
    private $userTransformer;
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $userRepo = new InMemoryUserRepository();
        $user = UserDataBuilder::aUser()->withId(
            UserIdDataBuilder::aUserId()->withValue("qwerty")->build()
        )->build();
        $userRepo->add($user);

        $this->user = $user;
        $this->userRepository = $userRepo;
        //$this->transformer = new MeasurementSummaryDtoDataTransformer();
    }

    protected function tearDown()
    {
        unset($this->userRepository);
        unset($this->userTransformer);
        unset($this->user);

        parent::tearDown();
    }
}
