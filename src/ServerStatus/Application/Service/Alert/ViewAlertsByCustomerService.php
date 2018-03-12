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

namespace ServerStatus\Application\Service\Alert;

use ServerStatus\Application\DataTransformer\Alert\CustomerAlertsDataTransformer;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Customer\CustomerDoesNotExistException;
use ServerStatus\Domain\Model\Customer\CustomerId;
use ServerStatus\Domain\Model\Customer\CustomerRepository;

class ViewAlertsByCustomerService
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var CustomerAlertsDataTransformer
     */
    private $transformer;


    public function __construct(CustomerRepository $customerRepository, CustomerAlertsDataTransformer $transformer)
    {
        $this->customerRepository = $customerRepository;
        $this->transformer = $transformer;
    }

    /**
     * @param ViewAlertsByCustomerRequest $request
     * @return array
     * @throws CustomerDoesNotExistException
     */
    public function execute(ViewAlertsByCustomerRequest $request)
    {
        $customer = $this->findCustomer($request->customerId());
        $this->transformer->write($customer);

        return $this->transformer->read();
    }

    /**
     * @param CustomerId $id
     * @return null|Customer
     * @throws CustomerDoesNotExistException
     */
    private function findCustomer(CustomerId $id)
    {
        $customer = $this->customerRepository->ofId($id);
        if (!$customer) {
            throw new CustomerDoesNotExistException(sprintf(
                'Customer "%s" not found while trying to view alerts',
                $id
            ));
        }

        return $customer;
    }
}
