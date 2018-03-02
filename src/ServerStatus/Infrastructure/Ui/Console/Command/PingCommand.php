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

use ServerStatus\Domain\Model\Check\CheckCollection;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementResult;
use ServerStatus\Infrastructure\Service\PingService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class PingCommand extends AbstractCommand
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

    public function __construct(
        CheckRepository $checkRepository,
        MeasurementRepository $measurementRepository,
        PingService $pingService
    ) {
        parent::__construct();
        $this->checkRepository = $checkRepository;
        $this->measurementRepository = $measurementRepository;
        $this->pingService = $pingService;
    }

    protected function configure()
    {
        $this
            ->setName('server-status:ping')
            ->setDescription('Pings defined checks')
            ->setHelp('This command allows you to ping defined checks')
            ->addOption(
                'go',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will save the measurements in the repository'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startWatch();
        $go = $input->getOption("go");


        $checks = $this->checkRepository->enabled();
        $urls = $checks->checkUrls();
        $output->writeln(sprintf('Checks found: %s, unique urls: %s', $checks->count(), sizeof($urls)));
        $errors = [];
        foreach ($urls as $url) {
            $output->write(sprintf("%s > ", (string) $url));
            try {
                $result = $this->pingService->measure($url);
                $output->write(sprintf(
                    "[%d] %s, ",
                    $result->status()->code(),
                    $result->duration()->formatted()
                ));
                $this->createMeasurements($checks->byCheckUrl($url), $result, $output, $go);
            } catch (\Exception $e) {
                $errors[] = $e;
                $output->write('<error>error</error>', true);
            }
        }

        $this->writeCompletedMessage($output, $this->stopWatch());

        if ($errors) {
            $output->writeln(sprintf('<error>%d errors found!</error>:', sizeof($errors)));
            foreach ($errors as $i => $error) {
                /* @var \Exception $error */
                $output->writeln(sprintf("%d. %s", $i + 1, $error->getMessage()));
            }
        }
        if (true !== $go) {
            $output->writeln('<comment>No measurements were saved, add --go option</comment>');
        }
    }

    private function createMeasurements(
        CheckCollection $checks,
        MeasurementResult $result,
        OutputInterface $output,
        bool $go = false
    ): void {
        $date = new \DateTimeImmutable("now");
        $measurements = [];
        foreach ($checks as $check) {
            $measurements[] = new Measurement($this->measurementRepository->nextId(), $date, $check, $result);
        }
        $numMeasurements = sizeof($measurements);
        if ($go) {
            $this->measurementRepository->add($measurements);
        }
        $output->write(sprintf(
            $go ? "<info>%s%s</info>" : "<comment>%s%s</comment>",
            $numMeasurements > 1 ? $numMeasurements . " " : "",
            $go ? "ok" : "not saved"
        ), true);
    }
}
