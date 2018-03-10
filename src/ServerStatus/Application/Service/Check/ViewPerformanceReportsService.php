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

namespace ServerStatus\Application\Service\Check;

use ServerStatus\Application\DataTransformer\Measurement\PerformanceReportsByCustomerDataTransformer;
use ServerStatus\Domain\Model\Check\CheckCollection;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReportFactory;

class ViewPerformanceReportsService
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

    /**
     * @var PerformanceReportsByCustomerDataTransformer
     */
    private $transformer;


    public function __construct(
        CustomerRepository $customerRepository,
        CheckRepository $checkRepository,
        MeasurementRepository $measurementRepository,
        PerformanceReportsByCustomerDataTransformer $transformer
    ) {
        $this->customerRepository = $customerRepository;
        $this->checkRepository = $checkRepository;
        $this->measurementRepository = $measurementRepository;
        $this->transformer = $transformer;
    }

    /**
     * @param ViewChecksByCustomerRequest $request
     * @return mixed
     * @throws CustomerDoesNotExistException
     */
    public function execute(ViewChecksByCustomerRequest $request)
    {
        $customer = $this->findCustomer($request->customerId());
        $dateRange = $request->dateRange();
        $this->transformer->write(
            $customer,
            $request->dateRange(),
            $this->createPerformanceReports($customer, $dateRange)
        );

        return $this->transformer->read();
    }

    /**
     * @param CustomerId $id
     * @return Customer
     * @throws CustomerDoesNotExistException
     */
    private function findCustomer(CustomerId $id): Customer
    {
        $customer = $this->customerRepository->ofId($id);
        if (!$customer) {
            throw new CustomerDoesNotExistException(sprintf(
                'Customer with id "%s" not found',
                $id
            ));
        }

        return $customer;
    }

    private function findChecksByCustomer(Customer $customer): CheckCollection
    {
        return $this->checkRepository->byCustomer($customer->id());
    }

    private function createPerformanceReports(Customer $customer, DateRange $dateRange): array
    {
        $performanceReports = [];
        $factory = new PerformanceReportFactory($this->measurementRepository);
        foreach ($this->findChecksByCustomer($customer) as $check) {
            $performanceReports[] = $factory->create($check, $dateRange);
        }

        return $performanceReports;
    }
}
