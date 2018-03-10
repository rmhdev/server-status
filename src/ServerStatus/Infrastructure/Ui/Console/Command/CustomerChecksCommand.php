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

use ServerStatus\Application\Service\Check\ViewChecksByCustomerRequest;
use ServerStatus\Application\Service\Check\ViewPerformanceReportsService;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Measurement\MeasurementStatus;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerChecksCommand extends AbstractCommand
{
    /**
     * @var ViewPerformanceReportsService
     */
    private $service;


    public function __construct(ViewPerformanceReportsService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    protected function configure()
    {
        $this
            ->setName('server-status:checks')
            ->setDescription('List checks for a given customer.')
            ->setHelp('This command allows you to show checks for a given customer.')
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
        $request = new ViewChecksByCustomerRequest(
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
            sprintf('Customer: %s', sprintf('<info>found</info> (%s)', $result["customer"]["email"]))
        );
        $output->writeln(
            sprintf('Date range: %s (%s)', $result["date_range"]["name"], $result["date_range"]["formatted"])
        );
        $output->writeln(
            sprintf('Checks: %d', sizeof($result["performance_reports"]))
        );
        foreach ($result["performance_reports"] as $i => $performanceReport) {
            $this->showPerformanceReport($output, $performanceReport, $i);
        }
    }

    private function showPerformanceReport(OutputInterface $output, array $performanceReport, int $pos = 0)
    {
        $output->writeln(sprintf(
            '%d. %s (%s): "%s"',
            $pos + 1,
            $performanceReport["check"]["name"],
            $performanceReport["check"]["slug"],
            $performanceReport["check"]["url"]["formatted"]
        ));

        $output->writeln(sprintf(
            '  uptime: %s, correct measurements: %s/%s',
            $performanceReport["performance"]["uptime"]["formatted"],
            $performanceReport["performance"]["measurements"]["correct"],
            $performanceReport["performance"]["measurements"]["total"]
        ));
        $output->writeln(sprintf(
            '  Response times (average; %s percentile): %s; %s',
            $performanceReport["performance"]["percentile"]["percent"]["formatted"],
            $performanceReport["performance"]["average"]["formatted"],
            $performanceReport["performance"]["percentile"]["average"]["formatted"]
        ));

        foreach ($performanceReport["performance"]["status"] as $status) {
            $output->writeln(sprintf(
                '  status %s x [%4s]: %s',
                $this->formatStatusCode($status["code"]),
                $status["count"],
                $status["average"]["formatted"]
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
