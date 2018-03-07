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

namespace ServerStatus\Tests\Domain\Model\Alert;

use PHPUnit\Framework\TestCase;

class AlertIdTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAUniqueIdWhenCreatedEmpty()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("")->build();

        $this->assertInternalType("string", $id->id());
        $this->assertGreaterThan(0, strlen($id->id()));
    }

    /**
     * @test
     */
    public function itShouldHaveValueWhenExplicitlyGiven()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", $id->id());
    }

    /**
     * @test
     */
    public function itShouldReturnValueWhenCastingToString()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("loremipsum")->build();

        $this->assertSame("loremipsum", (string) $id);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToBeComparedWithOtherIds()
    {
        $id = AlertIdDataBuilder::anAlertId()->withValue("loremipsum")->build();

        $this->assertFalse($id->equals(AlertIdDataBuilder::anAlertId()->withValue("123456")->build()));
        $this->assertTrue($id->equals($id));
    }
}
