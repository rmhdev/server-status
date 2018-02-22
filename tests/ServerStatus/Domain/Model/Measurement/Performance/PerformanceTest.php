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

use PHPUnit\Framework\TestCase;

class PerformanceTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldCalculateUptimePercent()
    {
        $performance = PerformanceDataBuilder::aPerformance()
            ->withTotalMeasurements(100)
            ->withSuccessfulMeasurements(99)
            ->build();

        $this->assertEquals(0.99, $performance->uptimePercent());
    }
}
