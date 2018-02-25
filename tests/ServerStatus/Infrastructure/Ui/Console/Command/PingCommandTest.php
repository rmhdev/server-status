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

use ServerStatus\Infrastructure\Persistence\InMemory\Check\InMemoryCheckRepository;
use ServerStatus\Infrastructure\Ui\Console\Command\PingCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PingCommandTest extends KernelTestCase
{
    /**
     * @test
     */
    public function itShouldCallAllAvailableChecksWhenExecuted()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($this->findCommand());

        $command = $application->find('server-status:ping');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Checks found: 3. Unique urls: 2', $output);
    }

    private function findCommand(): PingCommand
    {
        $repo = new InMemoryCheckRepository();

        return new PingCommand($repo);
    }
}
