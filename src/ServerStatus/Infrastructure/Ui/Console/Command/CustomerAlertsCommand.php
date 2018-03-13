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

use ServerStatus\Application\Service\Alert\ViewAlertsByCustomerRequest;
use ServerStatus\Application\Service\Alert\ViewAlertsByCustomerService;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Customer\CustomerId;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerAlertsCommand extends AbstractCommand
{
    /**
     * @var ViewAlertsByCustomerService
     */
    private $service;


    public function __construct(ViewAlertsByCustomerService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function configure()
    {
        $this
            ->setName('server-status:alerts')
            ->setDescription('List alerts for a given customer.')
            ->setHelp('This command allows you to show alerts for a given customer.')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the user.')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'The date of the report.', 'now')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'The type of report.', DateRangeLast24Hours::NAME)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startWatch();
        $this->executeService($input, $output);
        $this->writeCompletedMessage($output, $this->stopWatch());
    }

    private function executeService(InputInterface $input, OutputInterface $output)
    {
        $request = new ViewAlertsByCustomerRequest(
            new CustomerId($input->getArgument('id')),
            new \DateTimeImmutable($input->getOption('date')),
            $input->getOption('type')
        );
        try {
            $result = $this->service->execute($request);
        } catch (CustomerDoesNotExistException $exception) {
            $output->writeln('Customer: <error>not found</error>');
            return;
        }

        print_r($result); die();

        $output->writeln(sprintf('alerts: %d', sizeof($result["alerts"])));
    }
}
