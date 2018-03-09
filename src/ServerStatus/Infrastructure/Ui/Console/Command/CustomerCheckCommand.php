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

use ServerStatus\Application\Service\Check\ViewCheckByCustomerRequest;
use ServerStatus\Application\Service\Check\ViewCheckByCustomerService;
use ServerStatus\Domain\Model\Check\CheckDoesNotExistException;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Customer\CustomerId;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerCheckCommand extends AbstractCommand
{
    /**
     * @var ViewCheckByCustomerService
     */
    private $service;


    public function __construct(ViewCheckByCustomerService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function configure()
    {
        $this
            ->setName('server-status:check')
            ->setDescription('Display basic info for a given check.')
            ->setHelp('This command allows you to view average times for a given check.')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the user.')
            ->addArgument('check', InputArgument::REQUIRED, 'The slug of the check.')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'The date of the report.', 'now')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'The type of report.', DateRangeLast24Hours::NAME)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startWatch();
        $check = $this->findValues($input, $output);
        $this->printValues($output, $check);
        $this->writeCompletedMessage($output, $this->stopWatch());
    }

    private function findValues(InputInterface $input, OutputInterface $output): array
    {
        $slug = $input->getArgument('check');
        $request = new ViewCheckByCustomerRequest(
            new CustomerId($input->getArgument('id')),
            new CheckName($slug, $slug),
            new \DateTimeImmutable($input->getOption('date')),
            $input->getOption('type')
        );
        $check = [];
        try {
            $check = $this->service->execute($request);
        } catch (CustomerDoesNotExistException $exceptionA) {
            $output->writeln('Customer: <error>not found</error>');
        } catch (CheckDoesNotExistException $exceptionB) {
            $output->writeln('Check: <error>not found</error>');
        }

        return $check;
    }

    private function printValues(OutputInterface $output, array $values = [])
    {
        if (!$values) {
            return;
        }
        $output->writeln(sprintf(
            'Customer: <info>found</info> (%s): "%s"',
            $values["customer"]["id"],
            $values["customer"]["name"]
        ));
        $output->writeln(sprintf(
            'Check: <info>found</info> (%s): "%s"',
            $values["check"]["name"],
            $values["check"]["url"]["formatted"]
        ));
        $output->writeln(sprintf(
            'Date range: %s (%s)',
            $values["measure_summary"]["date_range"]["name"],
            $values["measure_summary"]["date_range"]["formatted"]
        ));

        foreach ($values["measure_summary"]["averages"] as $average) {
            $this->printAverage($average, $output);
        }
    }

    private function printAverage(array $average, OutputInterface $output)
    {
        $diff = "";
        if ($average["response_time"]["diff"]["value"]) {
            $diffText = sprintf(
                '%s%s',
                $average["response_time"]["diff"]["value"] > 0 ? '+' : '',
                $average["response_time"]["diff"]["formatted"]
            );
            $diff = sprintf(
                ' <%s>%10s</>',
                $average["response_time"]["diff"]["value"] < 0 ?
                    'fg=white;bg=green' :
                    ($average["response_time"]["diff"]["value"] == 0 ? 'info' : 'fg=white;bg=red'),
                $diffText
            );
        }
        $output->writeln(sprintf(
            "  %s = %s%10s",
            $average["response_time"]["date_range"]["formatted"],
            $average["response_time"]["formatted"],
            $diff
        ));
    }
}
