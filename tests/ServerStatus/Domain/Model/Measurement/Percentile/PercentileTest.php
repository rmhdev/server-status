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

namespace ServerStatus\Tests\Domain\Model\Measurement\Percentile;

use PHPUnit\Framework\TestCase;

class PercentileTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnZeroValuesWhenNoDataIsDefined()
    {
        $percentile = PercentileDataBuilder::aPercentile()->build();

        $this->assertEquals(PercentDataBuilder::aPercent()->withValue(0)->build(), $percentile->percent());
        $this->assertEquals(0, $percentile->value());
    }
}
