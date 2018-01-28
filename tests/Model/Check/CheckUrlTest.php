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

namespace ServerStatus\Tests\Model\Check;

use PHPUnit\Framework\TestCase;

class CheckUrlTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidMethodDataProvider
     * @expectedException \ServerStatus\Model\Check\InvalidCheckMethodException
     */
    public function itShouldThrowExceptionWhenUnexpectedMethodNameIsGiven($method)
    {
        CheckUrlDataBuilder::aCheckUrl()->withMethod($method)->build();
    }

    public function invalidMethodDataProvider()
    {
        return [
            ['HEAD'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
            ['PURGE'],
            ['OPTIONS'],
            ['TRACE'],
            ['CONNECT'],
        ];
    }

    /**
     * @test
     * @dataProvider validButNotFormattedMethodNames
     */
    public function itShouldAcceptValidButNonFormattedMethodNames($method, $expected)
    {
        $url = CheckUrlDataBuilder::aCheckUrl()->withMethod($method)->build();

        $this->assertEquals($expected, $url->method());
    }

    public function validButNotFormattedMethodNames()
    {
        return [
            ["get", "GET"],
            ["pOst", "POST"],
            [" GET", "GET"],
            [" PoST  ", "POST"],
        ];
    }
}
