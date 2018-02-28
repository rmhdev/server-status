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

use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatus;
use ServerStatus\Domain\Model\Measurement\Performance\PerformanceStatusCollection;

class PerformanceStatusCollectionDataBuilder
{
    /**
     * @var PerformanceStatus[]
     */
    private $values;

    public function __construct()
    {
        $this->values = [];
    }

    public function withValues($values): PerformanceStatusCollectionDataBuilder
    {
        $this->values = $values;

        return $this;
    }

    public function build(): PerformanceStatusCollection
    {
        return new PerformanceStatusCollection($this->values);
    }

    public static function aPerformanceStatusCollection(): PerformanceStatusCollectionDataBuilder
    {
        return new self();
    }
}
