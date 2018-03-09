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

use ServerStatus\Application\DataTransformer\Check\CheckDataTransformer;
use ServerStatus\Domain\Model\Check\CheckDoesNotExistException;
use ServerStatus\Domain\Model\Check\CheckRepository;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryFactory;
use ServerStatus\Domain\Model\Customer\CustomerRepository;

class ViewCheckByCustomerService
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
     * @var CheckDataTransformer
     */
    private $transformer;


    public function __construct(
        CustomerRepository $customerRepository,
        CheckRepository $checkRepository,
        MeasurementRepository $measurementRepository,
        CheckDataTransformer $transformer
    ) {
        $this->customerRepository = $customerRepository;
        $this->checkRepository = $checkRepository;
        $this->measurementRepository = $measurementRepository;
        $this->transformer = $transformer;
    }

    /**
     * @param ViewCheckByCustomerRequest $request
     * @return mixed
     * @throws CheckDoesNotExistException
     * @throws CustomerDoesNotExistException
     */
    public function execute(ViewCheckByCustomerRequest $request)
    {
        $customer = $this->customerRepository->ofId($request->customerId());
        if (!$customer) {
            throw new CustomerDoesNotExistException(sprintf(
                'Customer with id "%s" not found',
                $request->customerId()
            ));
        }
        $check = $this->checkRepository->byCustomerAndSlug($request->customerId(), $request->checkName());
        if (!$check) {
            throw new CheckDoesNotExistException(sprintf(
                'Check  "%s" by customer "%s" not found',
                $request->checkName()->slug(),
                $request->customerId()
            ));
        }


        $this->transformer->write($check, MeasureSummaryFactory::create(
            $check,
            $this->measurementRepository,
            $request->dateRange()
        ));

        return $this->transformer->read();
    }
}
