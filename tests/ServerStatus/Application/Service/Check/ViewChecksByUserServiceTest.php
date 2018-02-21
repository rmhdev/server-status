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
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\User\UserId;
use ServerStatus\Domain\Model\User\UserRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\User\InMemoryUserRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Measurement\MeasurementDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserDataBuilder;
use ServerStatus\Tests\Domain\Model\User\UserIdDataBuilder;

class ViewChecksByUserServiceTest extends TestCase
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CheckRepository
     */
    private $checkRepository;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;

    /**
     * @var UserChecksDataTransformer
     */
    private $userChecksTransformer;

    /**
     * @var UserId
     */
    private $userId;


    protected function setUp()
    {
        parent::setUp();

        $userId = UserIdDataBuilder::aUserId()->withValue("qwerty")->build();
        $user = UserDataBuilder::aUser()->withId($userId)->build();

        $userRepo = new InMemoryUserRepository();
        $userRepo->add($user);

        $checkRepo = new InMemoryCheckRepository();
        $checkRepo
            ->add(CheckDataBuilder::aCheck()->withUser($user)->build())
            ->add(CheckDataBuilder::aCheck()->withUser($user)->build())
            ->add(CheckDataBuilder::aCheck()->build())
        ;

        // fake measurements for $userId's checks:
        $measurementRepo = new InMemoryMeasurementRepository();
        foreach ($checkRepo->byUser($userId) as $check) {
            foreach ($this->createMeasurements($check) as $measurement) {
                $measurementRepo->add($measurement);
            }
        }

        $this->userId = $userId;
        $this->userRepository = $userRepo;
        $this->checkRepository = $checkRepo;
        $this->measurementRepository = $measurementRepo;
        $this->userChecksTransformer = new UserChecksDtoDataTransformer();
    }

    private function createMeasurements(Check $check)
    {
        return [
            MeasurementDataBuilder::aMeasurement()
                ->withCheck($check)
                ->withDate(new \DateTime("2018-02-03T00:00:00+0200"))
                ->build(),
            MeasurementDataBuilder::aMeasurement()
                ->withCheck($check)
                ->withDate(new \DateTime("2018-02-03T01:00:00+0200"))
                ->build(),
            MeasurementDataBuilder::aMeasurement()
                ->withCheck($check)
                ->withDate(new \DateTime("2018-02-03T02:00:00+0200"))
                ->build()
        ];
    }



    protected function tearDown()
    {
        unset($this->userChecksTransformer);
        unset($this->measurementRepository);
        unset($this->checkRepository);
        unset($this->userRepository);
        unset($this->userId);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function isShouldReturnEmptyListWhenNoRequestIsGiven()
    {
        $data = $this->createService()->execute();

        $this->assertEquals([], $data);
    }

    private function createService(): ViewChecksByUserService
    {
        return new ViewChecksByUserService(
            $this->userRepository,
            $this->checkRepository,
            $this->measurementRepository,
            $this->userChecksTransformer
        );
    }

    /**
     * @test
     */
    public function itShouldReturnUserWhenFound()
    {
        $data = $this->createService()->execute(
            new ViewChecksByUserRequest($this->userId)
        );

        $this->assertEquals($this->userId->value(), $data["user"]["id"]);
    }

    /**
     * @test
     */
    public function itShouldReturnChecksByUser()
    {
        $data = $this->createService()->execute(
            new ViewChecksByUserRequest($this->userId)
        );

        $this->assertEquals(2, sizeof($data["checks"]));
    }

    /**
     * @testNO
     */
    public function itShouldReturnMeasurementSummaryDataForEachCheck()
    {
        $data = $this->createService()->execute(
            new ViewChecksByUserRequest($this->userId, new \DateTime("2018-02-03T12:00:00+0200"), "day")
        );

        //$this->assertEquals("day", $data["checks"][0]["measurement_summary"]["name"]);
    }
}
