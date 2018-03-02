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

use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementDuration;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReportFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class CustomerChecksCommand extends AbstractCommand
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var CheckRepository
     */
    private $checkRepository;

    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;


    public function __construct(
        CustomerRepository $customerRepository,
        CheckRepository $checkRepository,
        MeasurementRepository $measurementRepository
    ) {
        parent::__construct();
        $this->customerRepository = $customerRepository;
        $this->checkRepository = $checkRepository;
        $this->measurementRepository = $measurementRepository;
    }

    protected function configure()
    {
        $this
            ->setName('server-status:checks')
            ->setDescription('List checks for a given customer.')
            ->setHelp('This command allows you to show checks for a given customer.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'The date of the report.', 'now')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'The type of report.', DateRangeLast24Hours::NAME)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startWatch();

        $email = $input->getArgument('email');
        $date = $input->getOption('date');
        $type = $input->getOption('type');
        $dateRange = DateRangeFactory::create($type, new \DateTimeImmutable($date));
        $customer = $this->customerRepository->ofId(new CustomerId($email));

        $output->writeln(sprintf('Customer: %s', $customer ? '<info>found</info>' : '<error>not found</error>'));
        if (!$customer) {
            return;
        }

        $checks = $this->checkRepository->byCustomer($customer->id());
        $output->writeln(sprintf('Checks: %d', $checks->count()));
        $output->writeln(sprintf('Date range: %s (%s)', $dateRange->name(), $dateRange->formatted()));

        foreach ($checks as $check) {
            $this->showCheck($check, $dateRange, $output);
        }

        $this->writeCompletedMessage($output, $this->stopWatch());
    }

    private function showCheck(Check $check, DateRange $dateRange, OutputInterface $output)
    {
        $output->writeln((string) $check->url());
        $factory = new PerformanceReportFactory($this->measurementRepository);
        $performanceReport = $factory->create($check, $dateRange);

        $percentile = $performanceReport->performance()->percentile();
        $output->writeln(sprintf(
            '  uptime: %s, mean response time: %s, %s percentile: %s',
            $performanceReport->performance()->uptimePercent(),
            $performanceReport->performance()->responseTimeMean()->formatted(),
            $percentile->percent(),
            (new MeasurementDuration($percentile->value()))->formatted()
        ));

        foreach ($performanceReport->performance()->performanceStatusCollection() as $performanceStatus) {
            $output->writeln(sprintf(
                '  status %s: %s',
                $this->formatStatusCode($performanceStatus->status()),
                $performanceStatus->durationAverage()->formatted()
            ));
        }
    }

    private function formatStatusCode($status)
    {
        if (is_numeric($status)) {
            $status = new MeasurementStatus($status);
        }
        if (!$status instanceof MeasurementStatus) {
            return (string) $status;
        }
        $tag = "info";
        if ($status->isClientError() || $status->isServerError()) {
            $tag = 'error';
        } elseif ($status->isInternalError()) {
            $tag = 'error';
        } elseif ($status->isInformational()) {
            $tag = 'comment';
        } elseif ($status->isRedirection()) {
            $tag = 'question';
        }
        return sprintf('<%s>%3s</%s>', $tag, $status->code(), $tag);
    }
}
