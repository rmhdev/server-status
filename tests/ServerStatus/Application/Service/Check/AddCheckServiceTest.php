<?php

/**
 * This file is part of the server-status package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Application\Service\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\Service\Check\AddCheckRequest;
use ServerStatus\Application\Service\Check\AddCheckService;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckNameDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckStatusDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckUrlDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class AddCheckServiceTest extends TestCase
{
    /**
     * @var CheckRepository
     */
    private static $repository;

    /**
     * @var AddCheckService
     */
    private static $service;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $checkRepo = new InMemoryCheckRepository();
        self::$repository = $checkRepo;
        self::$service = new AddCheckService($checkRepo);
    }

    public static function tearDownAfterClass()
    {
        static::$repository = null;
        static::$service = null;
        parent::tearDownAfterClass();
    }

    /**
     * @test
     */
    public function itShouldAddNewCheckInRepository()
    {
        $customer = CustomerDataBuilder::aCustomer()->withEmail("add-new-check@example.com")->build();
        $request = new AddCheckRequest(
            CheckNameDataBuilder::aCheckName()->withSlug("add-new-check")->build(),
            CheckUrlDataBuilder::aCheckUrl()->build(),
            CheckStatusDataBuilder::aCheckStatus()->build(),
            $customer
        );
        static::$service->execute($request);

        $this->assertEquals(
            1,
            self::$repository->byCustomer($customer->id())->count()
        );
        $newCheck = self::$repository->byCustomerAndSlug(
            $customer->id(),
            CheckNameDataBuilder::aCheckName()->withSlug("add-new-check")->build()
        );
        $this->assertNotNull($newCheck, 'The newly created check should be in repository');
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Check\CheckAlreadyExistException
     */
    public function itShouldThrowExceptionWhenAddingExistingCheckByCustomer()
    {
        $customer = CustomerDataBuilder::aCustomer()->withEmail("add-new-check-duplicate@example.com")->build();
        $request = new AddCheckRequest(
            CheckNameDataBuilder::aCheckName()->withSlug("add-new-check")->build(),
            CheckUrlDataBuilder::aCheckUrl()->build(),
            CheckStatusDataBuilder::aCheckStatus()->build(),
            $customer
        );
        static::$service->execute($request);
        // Adding a new check with same values as previous one should throw exception:
        static::$service->execute($request);
    }
}
