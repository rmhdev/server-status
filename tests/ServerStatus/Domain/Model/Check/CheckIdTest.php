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

namespace ServerStatus\Tests\Domain\Model\Check;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Check\CheckId;

class CheckIdTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAUniqueIdWhenCreatedEmpty()
    {
        $id = new CheckId();

        $this->assertInternalType("string", $id->value());
        $this->assertGreaterThan(0, strlen($id->value()));
    }

    /**
     * @test
     */
    public function itShouldHaveValueWhenExplicitlyGiven()
    {
        $id = new CheckId("loremipsum");

        $this->assertSame("loremipsum", $id->value());
    }

    /**
     * @test
     */
    public function itShouldReturnValueWhenCastingToString()
    {
        $id = new CheckId("loremipsum");

        $this->assertSame("loremipsum", (string) $id);
    }

    /**
     * @test
     */
    public function itShouldBeAbleToBeComparedWithOtherCheckIds()
    {
        $id = new CheckId("loremipsum");

        $this->assertFalse($id->equals(new CheckId("123456")));
        $this->assertTrue($id->equals($id));
    }
}
