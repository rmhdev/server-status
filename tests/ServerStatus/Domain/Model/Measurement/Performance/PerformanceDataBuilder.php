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

namespace ServerStatus\Tests\Domain\Model\Measurement\Performance;

use ServerStatus\Domain\Model\Measurement\Percentile\Percentile;
use ServerStatus\Domain\Model\Measurement\Performance\Performance;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatusCollection;
use ServerStatus\Tests\Domain\Model\Measurement\Percentile\PercentileDataBuilder;

class PerformanceDataBuilder
{
    /**
     * @var PerformanceStatusCollection
     */
    private $performanceStatusCollection;

    /**
     * @var Percentile
     */
    private $percentile;


    public function __construct()
    {
        $this->performanceStatusCollection = PerformanceStatusCollectionDataBuilder::aPerformanceStatusCollection()
            ->build();
        $this->percentile = PercentileDataBuilder::aPercentile()->build();
    }

    public function withPerformanceStatusCollection(PerformanceStatusCollection $collection): PerformanceDataBuilder
    {
        $this->performanceStatusCollection = $collection;

        return $this;
    }

    public function withPercentile(Percentile $percentile): PerformanceDataBuilder
    {
        $this->percentile = $percentile;

        return $this;
    }

    public function build(): Performance
    {
        return new Performance($this->performanceStatusCollection, $this->percentile);
    }

    public static function aPerformance(): PerformanceDataBuilder
    {
        return new self();
    }
}
