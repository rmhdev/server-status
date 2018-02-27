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

namespace ServerStatus\Tests\Domain\Model\Measurement;

use PHPUnit\Framework\TestCase;

class MeasurementResultTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnZeroWhenNotMemoryIsDefined()
    {
        $result = MeasurementResultDataBuilder::aMeasurementResult()->build();

        $this->assertEquals(0, $result->memory());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function itShouldThrowExceptionWhenMemoryIsNegative()
    {
        MeasurementResultDataBuilder::aMeasurementResult()->withMemory(-1)->build();
    }
}
