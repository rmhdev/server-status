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

class MeasurementIdTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAUniqueIdWhenCreatedEmpty()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("")->build();

        $this->assertInternalType("string", $id->value());
        $this->assertGreaterThan(0, strlen($id->value()));
    }

    /**
     * @test
     */
    public function itShouldHaveValueWhenExplicitlyGiven()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", $id->value());
    }

    /**
     * @test
     */
    public function itShouldReturnValueWhenCastingToString()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", (string) $id);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToBeComparedWithOtherCheckIds()
    {
        $id = MeasurementIdDataBuilder::aMeasurementId()->withValue("loremipsum")->build();

        $this->assertFalse($id->equals(MeasurementIdDataBuilder::aMeasurementId()->withValue("123456")->build()));
        $this->assertTrue($id->equals($id));
    }
}
