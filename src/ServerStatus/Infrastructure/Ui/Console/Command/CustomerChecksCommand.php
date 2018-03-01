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
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerChecksCommand extends Command
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
        $email = $input->getArgument('email');
        $date = $input->getOption('date');
        $type = $input->getOption('type');
        $dateRange = DateRangeFactory::create($type, new \DateTimeImmutable($date));

        $customer = $this->customerRepository->ofId(new CustomerId($email));

        $output->writeln(sprintf(
            'Customer: %s',
            $customer ? '<info>found</info>' : '<error>not found</error>'
        ));

        $checks = $this->checkRepository->byCustomer($customer->id());
        $output->writeln(sprintf(
            'Checks: %d',
            $checks->count()
        ));
        $output->writeln(sprintf(
            'Date range: %s (%s)',
            $dateRange->name(),
            $dateRange->formatted()
        ));
    }
}
