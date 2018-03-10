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

use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceReport;

final class PerformanceReportsByCustomerDtoDataTransformer implements PerformanceReportsByCustomerDataTransformer
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var DateRange
     */
    private $dateRange;

    /**
     * @var PerformanceReport[]
     */
    private $performanceReports;


    /**
     * @inheritdoc
     */
    public function write(Customer $customer, DateRange $dateRange, $performanceReports)
    {
        $this->customer = $customer;
        $this->dateRange = $dateRange;
        $this->performanceReports = $performanceReports;
    }

    public function read()
    {
        return [
            "customer" => [
                "id" => $this->customer->id()->id(),
                "name" => $this->customer->screenName(),
                "alias" => $this->customer->alias(),
                "email" => $this->customer->email(),
                "status" => [
                    "name" => $this->customer->status()->name(),
                    "is_enabled" => $this->customer->status()->isEnabled(),
                ]
            ],
            "date_range" => [
                "from" => $this->dateRange->from()->format(DATE_ISO8601),
                "to" => $this->dateRange->to()->format(DATE_ISO8601),
                "name" => $this->dateRange->name(),
                "formatted" => $this->dateRange->formatted(),
            ],
            "performance_reports" => $this->readPerformanceReports(),
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
