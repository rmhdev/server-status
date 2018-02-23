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

namespace ServerStatus\Application\DataTransformer\Customer;

use ServerStatus\Application\DataTransformer\Measurement\MeasurementSummaryDtoDataTransformer;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;
use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryCollection;
use ServerStatus\Domain\Model\Check\CheckCollection;
use ServerStatus\Domain\Model\Customer\Customer;

final class CustomerChecksDtoDataTransformer implements CustomerChecksDataTransformer
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var CheckCollection
     */
    private $checkCollection;

    /**
     * @var MeasureSummaryCollection
     */
    private $measureSummaries;


    public function write(
        Customer $customer,
        CheckCollection $checkCollection,
        MeasureSummaryCollection $measureSummaries
    ) {
        $this->customer = $customer;
        $this->checkCollection = $checkCollection;
        $this->measureSummaries = $measureSummaries;
    }

    public function read()
    {
        return [
            "customer" => $this->processCustomer(),
            "checks" => $this->processChecks(),
        ];
    }

    private function processCustomer(): array
    {
        return [
            "id" => (string) $this->customer->id(),
            "alias" => (string) $this->customer->alias(),
        ];
    }

    private function processChecks(): array
    {
        $values = [];
        foreach ($this->checkCollection as $check) {
            $values[] = $this->processCheck($check);
        }

        return $values;
    }

    private function processCheck(Check $check): array
    {
        return [
            "id" => (string) $check->id(),
            "name" => (string) $check->name()->value(),
            "slug" => (string) $check->name()->slug(),
            "measure_summary" => $this->processMeasureSummary($check)
        ];
    }

    private function processMeasureSummary(Check $check): array
    {
        /* @var MeasureSummary $measureSummary */
        $measureSummary = $this->measureSummaries->byCheckId($check->id())->getIterator()->current();
        $transformer = new MeasurementSummaryDtoDataTransformer();
        $transformer->write($measureSummary);

        return $transformer->read();
    }
}
