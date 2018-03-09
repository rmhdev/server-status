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
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckDoesNotExistException;
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryFactory;
use ServerStatus\Domain\Model\Measurement\Summary\SummaryAverage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerCheckCommand extends AbstractCommand
{
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

        $slug = $input->getArgument('check');
        $request = new ViewCheckByCustomerRequest(
            new CustomerId($input->getArgument('id')),
            new CheckName($slug, $slug),
            new \DateTimeImmutable($input->getOption('date')),
            $input->getOption('type')
        );
        try {
            $this->service->execute($request);
        } catch (CustomerDoesNotExistException $exceptionA) {
            $output->writeln('Customer: <error>not found</error>');
            return;
        } catch (CheckDoesNotExistException $exceptionB) {
            $output->writeln('Check: <error>not found</error>');
            return;
        }



        $this->writeCompletedMessage($output, $this->stopWatch());
    }

    private function findCustomer(InputInterface $input, OutputInterface $output): ?Customer
    {
        $email = $input->getArgument('email');
        $customer = $this->customerRepository->ofEmail(new CustomerEmail($email));
        $output->writeln(sprintf(
            'Customer: %s',
            $customer ?
                sprintf('<info>found</info> (%s)', $customer->email()) :
                '<error>not found</error>'
        ));

        return $customer;
    }

    private function findCheck(Customer $customer, InputInterface $input, OutputInterface $output): ?Check
    {
        $checkSlug = $input->getArgument('check');
        $check = $this->checkRepository->byCustomerAndSlug($customer->id(), new CheckName($checkSlug, $checkSlug));
        $output->writeln(sprintf(
            'Check: %s',
            $check ?
                sprintf('<info>found</info> (%s): "%s"', $check->name(), $check->url()) :
                '<error>not found</error>'
        ));

        return $check;
    }

    private function createDateRange(InputInterface $input, OutputInterface $output): DateRange
    {
        $date = $input->getOption('date');
        $type = $input->getOption('type');
        $dateRange = DateRangeFactory::create($type, new \DateTimeImmutable($date));

        $output->writeln(sprintf('Date range: %s (%s)', $dateRange->name(), $dateRange->formatted()));

        return $dateRange;
    }

    private function createSummary(Check $check, DateRange $dateRange): MeasureSummary
    {
        return MeasureSummaryFactory::create($check, $this->measurementRepository, $dateRange);
    }

    private function printAverage(SummaryAverage $average, OutputInterface $output, SummaryAverage $previous = null)
    {
        $diff = "";
        if ($previous) {
            $durationDiff = $average->responseTime()->diff($previous->responseTime());
            $diffText = sprintf('%s%s', $durationDiff->decimal() > 0 ? '+' : '', $durationDiff);
            $diff = sprintf(
                ' <%s>%10s</>',
                $durationDiff->decimal() < 0 ?
                    'fg=white;bg=green' :
                    ($durationDiff->decimal() == 0 ? '' : 'fg=white;bg=red'),
                $diffText
            );
        }

        $output->writeln(sprintf("  %s = %s%10s", $average->dateRange(), $average->responseTime(), $diff));
    }
}
