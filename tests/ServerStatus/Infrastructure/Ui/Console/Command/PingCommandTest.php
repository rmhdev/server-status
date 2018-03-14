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

namespace ServerStatus\Tests\Infrastructure\Ui\Console\Command;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Mock\Client as MockClient;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Infrastructure\Persistence\InMemory\Measurement\InMemoryMeasurementRepository;
use ServerStatus\Infrastructure\Service\Ping\HttpPingService;
use ServerStatus\Infrastructure\Service\Ping\PingService;
use ServerStatus\Infrastructure\Ui\Console\Command\PingCommand;
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;
use ServerStatus\Tests\Domain\Model\Check\CheckUrlDataBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PingCommandTest extends KernelTestCase
{
    /**
     * @var CheckRepository
     */
    private $checkRepository;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;

    /**
     * @var PingService
     */
    private $pingService;


    protected function setUp()
    {
        parent::setUp();
        $this->checkRepository = $this->createCheckRepository();
        $this->measurementRepository = $this->createMeasurementRepository();
        $this->pingService = $this->createPingService();
    }

    protected function tearDown()
    {
        unset($this->checkRepository);
        unset($this->measurementRepository);
        unset($this->pingService);
        parent::tearDown();
    }


    private function createCheckRepository(): CheckRepository
    {
        $repo = new InMemoryCheckRepository();
        $repo
            ->add(
                CheckDataBuilder::aCheck()->withUrl(
                    CheckUrlDataBuilder::aCheckUrl()->withDomain("site.example.com")->build()
                )->build()
            )
            ->add(
                CheckDataBuilder::aCheck()->withUrl(
                    CheckUrlDataBuilder::aCheckUrl()->withDomain("example.io")->build()
                )->build()
            )
            ->add(
                CheckDataBuilder::aCheck()->withUrl(
                    CheckUrlDataBuilder::aCheckUrl()->withDomain("site.example.com")->build()
                )->build()
            )
        ;

        return $repo;
    }

    private function createMeasurementRepository(): MeasurementRepository
    {
        return new InMemoryMeasurementRepository();
    }

    private function createPingService(): PingService
    {
        $client = new MockClient();

        return new HttpPingService($client, MessageFactoryDiscovery::find());
    }

    /**
     * @test
     */
    public function itShouldCallAllAvailableChecksWhenExecuted()
    {
        // Number of measurements before executing the command.
        $countMeasurementsBefore = $this->measurementRepository->countAll();

        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($this->findCommand());
        $command = $application->find('server-status:ping');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),
            '--go'     => true
        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('Checks found: 3, unique urls: 2', $output);

        $countMeasurementsAfter = $this->measurementRepository->countAll();
        $this->assertEquals(3, $countMeasurementsAfter - $countMeasurementsBefore);
    }

    private function findCommand(): PingCommand
    {
        return new PingCommand($this->checkRepository, $this->measurementRepository, $this->pingService);
    }
}
