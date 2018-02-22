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

namespace ServerStatus\Tests\Domain\Model\Measurement\Summary;

use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummaryCollection;

class MeasureSummaryCollectionDataBuilder
{
    private $checks;

    public function __construct()
    {
        $this->checks = [];
    }

    public function withChecks($checks = []): MeasureSummaryCollectionDataBuilder
    {
        $this->checks = $checks;

        return $this;
    }

    public function build(): MeasureSummaryCollection
    {
        return new MeasureSummaryCollection($this->checks);
    }

    public static function aMeasureSummaryCollection(): MeasureSummaryCollectionDataBuilder
    {
        return new self();
    }
}
