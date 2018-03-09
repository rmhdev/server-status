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

use ServerStatus\Application\DataTransformer\Customer\CustomerChecksDataTransformer;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryCollection;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryFactory;
use ServerStatus\Domain\Model\Customer\CustomerRepository;

class ViewChecksByCustomerService
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
     * @var CustomerChecksDataTransformer
     */
    private $transformer;


    public function __construct(
        CustomerRepository $customerRepository,
        CheckRepository $checkRepository,
        MeasurementRepository $measurementRepository,
        CustomerChecksDataTransformer $transformer
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
        $customer = $this->customerRepository->ofId($request->customerId());
        if (!$customer) {
            throw new CustomerDoesNotExistException(sprintf(
                'Customer with id "%s" not found',
                $request->customerId()
            ));
        }
        $checkCollection = $this->checkRepository->byCustomer($request->customerId());

        $summaries = [];
        foreach ($checkCollection as $check) {
            $summaries[] = MeasureSummaryFactory::create(
                $check,
                $this->measurementRepository,
                $request->dateRange()
            );
        }
        $this->transformer->write(
            $customer,
            $checkCollection,
            new MeasureSummaryCollection($summaries)
        );

        return $this->transformer->read();
    }
}
