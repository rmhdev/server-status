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

namespace ServerStatus\Infrastructure\Ui\Console\Command;

use ServerStatus\Domain\Model\Check\CheckRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PingCommand extends Command
{
    private $checkRepository;

    public function __construct(CheckRepository $checkRepository)
    {
        parent::__construct();
        $this->checkRepository = $checkRepository;
    }

    private function checkRepository(): CheckRepository
    {
        return $this->checkRepository;
    }

    protected function configure()
    {
        $this
            ->setName('server-status:ping')
            ->setDescription('Pings defined checks')
            ->setHelp('This command allows you to ping defined checks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write("hi");
    }
}
