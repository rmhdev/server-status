<?php

/**
 * This file is part of the server-status package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Application\Service\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Application\Service\Check\UpdateCheckRequest;
use ServerStatus\Application\Service\Check\UpdateCheckService;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Check\CheckStatus;
use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckNameDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckStatusDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckUrlDataBuilder;

class UpdateCheckServiceTest extends TestCase
{
    /**
     * @var CheckRepository
     */
    private $repository;

    /**
     * @var UpdateCheckService
     */
    private $service;


    protected function setUp()
    {
        parent::setUp();
        $this->repository = new InMemoryCheckRepository();
        $this->service = new UpdateCheckService($this->repository);
    }

    protected function tearDown()
    {
        unset($this->service);
        unset($this->repository);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function itShouldUpdateValues()
    {
        $check = CheckDataBuilder::aCheck()
            ->withStatus(CheckStatus::CODE_ENABLED)
            ->build();
        $this->repository->add($check);
        $request = new UpdateCheckRequest(
            $check->id(),
            CheckNameDataBuilder::aCheckName()->withSlug("edited-check")->build(),
            CheckUrlDataBuilder::aCheckUrl()->withDomain("edited-domain.com")->build(),
            CheckStatusDataBuilder::aCheckStatus()->withCode(CheckStatus::CODE_DISABLED)->build()
        );
        $this->service->execute($request);
        $editedCheck = $this->repository->ofId($check->id());

        $this->assertEquals("edited-check", $editedCheck->name()->slug());
        $this->assertEquals("edited-domain.com", $editedCheck->url()->domain());
        $this->assertEquals(CheckStatus::CODE_DISABLED, $editedCheck->status()->name());
    }
}
