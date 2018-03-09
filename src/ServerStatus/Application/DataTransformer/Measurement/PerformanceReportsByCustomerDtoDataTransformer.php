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

namespace ServerStatus\Application\DataTransformer\Measurement;

use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReport;

final class PerformanceReportsByCustomerDtoDataTransformer implements PerformanceReportsByCustomerDataTransformer
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var PerformanceReport[]
     */
    private $performanceReports;


    /**
     * @inheritdoc
     */
    public function write(Customer $customer, $performanceReports)
    {
        $this->customer = $customer;
        $this->performanceReports = $performanceReports;
    }

    public function read()
    {
        return [
            "customer" => $this->readCustomer(),
            "performance_reports" => $this->readPerformanceReports(),
        ];
    }

    private function readCustomer()
    {
        return [
            "name" => $this->customer->screenName(),
            "alias" => $this->customer->alias(),
            "email" => $this->customer->email(),
            "status" => [
                "name" => $this->customer->status()->name(),
                "is_enabled" => $this->customer->status()->isEnabled(),
            ]
        ];
    }

    private function readPerformanceReports()
    {
        $formatter = new PerformanceReportDtoDataTransformer();
        $values = [];
        foreach ($this->performanceReports as $performanceReport) {
            $formatter->write($performanceReport);
            $values[] = $formatter->read();
        }

        return $values;
    }
}
