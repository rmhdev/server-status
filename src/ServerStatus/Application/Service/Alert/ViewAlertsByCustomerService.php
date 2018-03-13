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
use ServerStatus\Domain\Model\Alert\AlertRepository;
use ServerStatus\Domain\Model\AlertNotification\AlertNotificationRepository;
use ServerStatus\Domain\Model\AlertNotification\Log\AlertNotificationsLogCollection;
use ServerStatus\Domain\Model\AlertNotification\Log\AlertNotificationsLogFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
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
     * @var AlertRepository
     */
    private $alertRepository;

    /**
     * @var AlertNotificationRepository
     */
    private $alertNotificationRepository;

    /**
     * @var CustomerAlertsDataTransformer
     */
    private $transformer;


    public function __construct(
        CustomerRepository $customerRepository,
        AlertRepository $alertRepository,
        AlertNotificationRepository $alertNotificationRepository,
        CustomerAlertsDataTransformer $transformer
    ) {
        $this->customerRepository = $customerRepository;
        $this->alertRepository = $alertRepository;
        $this->alertNotificationRepository = $alertNotificationRepository;
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
        $this->transformer->write(
            $customer,
            $request->dateRange(),
            $this->findAlertNotificationsLogCollection($customer->id(), $request->dateRange())
        );

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

    private function findAlertNotificationsLogCollection(
        CustomerId $id,
        DateRange $dateRange
    ): AlertNotificationsLogCollection {
        return AlertNotificationsLogFactory::createCollection(
            $this->alertRepository->byCustomer($id),
            $dateRange,
            $this->alertNotificationRepository
        );
    }
}
