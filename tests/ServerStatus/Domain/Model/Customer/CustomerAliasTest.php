<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Domain\Model\Customer;

use PHPUnit\Framework\TestCase;

class CustomerAliasTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldAcceptEmptyAlias()
    {
        $alias = CustomerAliasDataBuilder::aCustomerAlias()->withValue("")->build();

        $this->assertEquals("", $alias->value());
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Customer\InvalidCustomerAliasException
     */
    public function itShouldThrowExceptionWithVeryLongValues()
    {
        $maxLengthAlias = str_repeat("a", 50);
        CustomerAliasDataBuilder::aCustomerAlias()->withValue($maxLengthAlias . "1")->build();
    }

    /**
     * @test
     * @dataProvider longNames
     */
    public function itShouldAcceptedNamesWithMaxLength($longName)
    {
        $name = CustomerAliasDataBuilder::aCustomerAlias()->withValue($longName)->build();

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
        $name = CustomerAliasDataBuilder::aCustomerAlias()->withValue("   Lorem Ipsum ")->build();

        $this->assertSame("Lorem Ipsum", $name->value());
    }

    /**
     * @test
     */
    public function itShouldIgnoreLeftAndRightSpacesFromNameLengthCalculation()
    {
        $name = CustomerAliasDataBuilder::aCustomerAlias()->withValue("   " . $this->longName() . " ")->build();

        $this->assertEquals($this->longName(), $name->value());
    }

    /**
     * @test
     */
    public function itShouldDetectIfAliasIsEmpty()
    {
        $empty = CustomerAliasDataBuilder::aCustomerAlias()->withValue("")->build();
        $this->assertTrue($empty->isEmpty());

        $alias = CustomerAliasDataBuilder::aCustomerAlias()->withValue("Lorem Ipsum")->build();
        $this->assertFalse($alias->isEmpty());
    }
}
