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
        $output->writeln(
            sprintf('Customer: <info>found</info> (%s)', $result["customer"]["email"])
        );
        $output->writeln(
            sprintf('Date range: %s (%s)', $result["date_range"]["name"], $result["date_range"]["formatted"])
        );
        $output->writeln(
            sprintf('Alerts: %d', sizeof($result["alerts"]))
        );
        foreach ($result["alerts"] as $i => $alert) {
            $this->writeAlert($output, $alert, $i);
        }
    }


    private function writeAlert(OutputInterface $output, array $alert, int $pos = 0)
    {
        $output->writeln(sprintf(
            '%d. <fg=white;bg=%s> %s </> Alert if "%s" happens during the last %d minutes',
            $pos + 1,
            $alert["is_enabled"] ? 'green' : 'red',
            $alert["is_enabled"] ? 'enabled' : 'disabled',
            $alert["reason"]["name"],
            $alert["time_window"]["minutes"]
        ));
        $this->writeCheck($output, $alert["check"]);
        $output->writeln(
            sprintf('  Channel: %s (%s)', $alert["channel"]["name"], $alert["channel"]["destination_raw"])
        );
        $output->writeln(
            sprintf('  Notifications: %d', $alert["notifications"]["total"])
        );
    }

    private function writeCheck(OutputInterface $output, array $check = [])
    {
        if (!$check) {
            $output->writeln('  Check: ALL by customer');
            return;
        }
        $output->writeln(
            sprintf('  Check: %s (%s)', $check["name"], $check["url"]["formatted"])
        );
    }
}
