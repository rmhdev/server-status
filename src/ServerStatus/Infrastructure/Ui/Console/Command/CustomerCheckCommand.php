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
use ServerStatus\Domain\Model\Check\CheckName;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerEmail;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerCheckCommand extends AbstractCommand
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
            ->setName('server-status:check')
            ->setDescription('Display basic graph for a given check.')
            ->setHelp('This command allows you to show a graph for a given check.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('check', InputArgument::REQUIRED, 'The slug of the check.')
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'The date of the report.', 'now')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'The type of report.', DateRangeLast24Hours::NAME)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startWatch();

        $customer = $this->findCustomer($input, $output);
        if (!$customer) {
            return;
        }
        $check = $this->findCheck($customer, $input, $output);
        if (!$check) {
            return;
        }
        $dateRange = $this->createDateRange($input, $output);


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
}
