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

namespace ServerStatus\Domain\Model\Measurement\Summary;

use ServerStatus\Domain\Model\Check\CheckId;
use ServerStatus\Domain\Model\Customer\CustomerId;

final class MeasureSummaryCollection implements \Countable, \IteratorAggregate
{
    private $measureSummaries;

    public function __construct($measureSummaries = [])
    {
        $this->measureSummaries = $this->processValues($measureSummaries);
    }

    private function processValues($measureSummaries = []): \ArrayIterator
    {
        if (!\is_iterable($measureSummaries)) {
            $measureSummaries = [$measureSummaries];
        }
        $values = [];
        foreach ($measureSummaries as $measureSummary) {
            $this->assertMeasureSummary($measureSummary);
            $values[] = $measureSummary;
        }

        return new \ArrayIterator($values);
    }

    private function assertMeasureSummary($measureSummary)
    {
        if (!is_object($measureSummary)) {
            throw new \UnexpectedValueException(sprintf(
                'Collection only accepts "MeasureSummary" objects, "%s" received',
                $measureSummary
            ));
        }
        if (!$measureSummary instanceof MeasureSummary) {
            throw new \UnexpectedValueException(sprintf(
                'Collection only accepts "MeasureSummary" objects, "%s" received',
                get_class($measureSummary)
            ));
        }
    }

    private function measureSummaries(): \ArrayIterator
    {
        return $this->measureSummaries;
    }

    public function count(): int
    {
        return $this->measureSummaries()->count();
    }

    public function getIterator(): \ArrayIterator
    {
        return $this->measureSummaries();
    }

    public function byCustomerId(CustomerId $id): MeasureSummaryCollection
    {
        return new self(
            new \CallbackFilterIterator(
                $this->measureSummaries(),
                function ($current, $key, $iterator) use ($id) {
                    /* @var MeasureSummary $current */
                    return $current->check()->customer()->id()->equals($id);
                }
            )
        );
    }

    public function byCheckId(CheckId $checkId): MeasureSummaryCollection
    {
        return new self(
            new \CallbackFilterIterator(
                $this->measureSummaries(),
                function ($current, $key, $iterator) use ($checkId) {
                    /* @var MeasureSummary $current */
                    return $current->check()->id()->equals($checkId);
                }
            )
        );
    }
}
