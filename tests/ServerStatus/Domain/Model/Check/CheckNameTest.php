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
use ServerStatus\Domain\Model\Check\CheckName;

class CheckNameTest extends TestCase
{
    /**
     * @test
     * @dataProvider emptyNames
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionWhenNameIsEmpty($name)
    {
        $this->createCheckNameWithValue($name);
    }

    public function emptyNames()
    {
        return [
            [""], [null], ["  "]
        ];
    }

    private function createCheckNameWithValue($name = "", $slug = "")
    {
        return new CheckName($name, $slug);
    }

    /**
     * @test
     * @dataProvider longNames
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionWhenNameIsTooLong($name)
    {
        $this->createCheckNameWithValue($name . "1");
    }

    /**
     * @test
     * @dataProvider longNames
     */
    public function itShouldAcceptedNamesWithMaxLength($longName)
    {
        $name = $this->createCheckNameWithValue($longName);

        $this->assertEquals($longName, $name->value());
    }

    public function longNames()
    {
        return [
            [str_repeat("abcdefghij", 6)],
            [str_repeat('ترحيبترحيب', 6)],
            [str_repeat("русскийязы", 6)]
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
        $name = $this->createCheckNameWithValue("   Lorem Ipsum ");

        $this->assertSame("Lorem Ipsum", $name->value());
    }

    /**
     * @test
     */
    public function itShouldIgnoreLeftAndRightSpacesFromNameLengthCalculation()
    {
        $name = $this->createCheckNameWithValue("   " . $this->longName() . " ");

        $this->assertSame(CheckName::MAX_LENGTH, strlen($name->value()));
    }

    /**
     * @test
     * @dataProvider slugsDataProvider
     */
    public function itShouldGenerateASLugWhenItIsEmpty(string $raw, string $expected)
    {
        $name = $this->createCheckNameWithValue($raw);

        $this->assertSame($expected, $name->slug());
    }

    public function slugsDataProvider()
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield ['  Lorem Ipsum  ', 'lorem-ipsum'];
        yield [' lOrEm  iPsUm  ', 'lorem-ipsum'];
        yield ['<p>Lorem Ipsum</p>', 'lorem-ipsum'];
        yield ['!Lorem Ipsum!', '!lorem-ipsum!'];
        yield ['lorem-ipsum', 'lorem-ipsum'];
        yield ['lorem 日本語 ipsum', 'lorem-日本語-ipsum'];
        yield ['lorem русский язык ipsum', 'lorem-русский-язык-ipsum'];
        yield ['lorem العَرَبِيَّة‎‎ ipsum', 'lorem-العَرَبِيَّة‎‎-ipsum'];
    }

    /**
     * @test
     */
    public function itShouldReturnDefinedSlug()
    {
        $name = $this->createCheckNameWithValue("Name", "custom-slug");

        $this->assertEquals("custom-slug", $name->slug());
    }

    /**
     * @test
     */
    public function itShouldTrimSlug()
    {
        $name = $this->createCheckNameWithValue("Name", " custom-slug  ");

        $this->assertEquals("custom-slug", $name->slug());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionWhenSlugIsNotStandard()
    {
        $this->createCheckNameWithValue("Name", "My incorrect slug");
    }

    /**
     * @test
     * @dataProvider longNames
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionWhenSlugIsTooLong($longSlug)
    {
        $this->createCheckNameWithValue("Name", $longSlug . "a");
    }

    /**
     * @test
     */
    public function itShouldReturnNameWhenConvertedToString()
    {
        $name = $this->createCheckNameWithValue("My Custom check", "other-value");

        $this->assertSame("My Custom check", (string) $name);
    }
}
