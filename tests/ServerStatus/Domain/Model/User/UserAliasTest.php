<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Domain\Model\User;

use PHPUnit\Framework\TestCase;
use ServerStatus\Tests\Domain\Model\User\UserAliasDataBuilder;

class UserAliasTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptEmptyAlias()
    {
        $alias = UserAliasDataBuilder::aUserAlias()->withValue("")->build();

        $this->assertEquals("", $alias->value());
    }

    /**
     * @test
     * @expectedException \ServerStatus\ServerStatus\Domain\Model\User\InvalidUserAliasException
     */
    public function itShouldThrowExceptionWithVeryLongValues()
    {
        $maxLengthAlias = str_repeat("a", 50);
        UserAliasDataBuilder::aUserAlias()->withValue($maxLengthAlias . "1")->build();
    }

    /**
     * @test
     * @dataProvider longNames
     */
    public function itShouldAcceptedNamesWithMaxLength($longName)
    {
        $name = UserAliasDataBuilder::aUserAlias()->withValue($longName)->build();

        $this->assertEquals($longName, $name->value());
    }

    public function longNames()
    {
        return [
            [str_repeat("abcdefghij", 2)],
            [str_repeat('ترحيبترحيب', 2)],
            [str_repeat("русскийязы", 2)]
        ];
    }

    private function longName(): string
    {
        foreach ($this->longNames() as $longName) {
            return $longName[0];
        }
        throw new \UnexpectedValueException("No long name defined");
    }

    /**
     * @test
     */
    public function itShouldTrimNamesWithLeftOrRightSpaces()
    {
        $name = UserAliasDataBuilder::aUserAlias()->withValue("   Lorem Ipsum ")->build();

        $this->assertSame("Lorem Ipsum", $name->value());
    }

    /**
     * @test
     */
    public function itShouldIgnoreLeftAndRightSpacesFromNameLengthCalculation()
    {
        $name = UserAliasDataBuilder::aUserAlias()->withValue("   " . $this->longName() . " ")->build();

        $this->assertEquals($this->longName(), $name->value());
    }
}
