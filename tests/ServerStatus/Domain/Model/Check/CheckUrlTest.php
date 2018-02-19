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
use ServerStatus\Domain\Model\Check\InvalidCheckDomainException;

class CheckUrlTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidMethodDataProvider
     * @expectedException \ServerStatus\Domain\Model\Check\InvalidCheckMethodException
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

    /**
     * @test
     * @dataProvider validButNotFormattedProtocolNames
     */
    public function isShouldAcceptNonFormattedProtocol($protocol, $expected)
    {
        $url = CheckUrlDataBuilder::aCheckUrl()->withProtocol($protocol)->build();

        $this->assertEquals($expected, $url->protocol());
    }

    public function validButNotFormattedProtocolNames()
    {
        return [
            ["HTTP", "http"],
            ["HttPS", "https"],
            [" htTP", "http"],
            [" hTTpS  ", "https"],
        ];
    }

    /**
     * @test
     * @dataProvider invalidProtocolDataProvider
     * @expectedException \ServerStatus\Domain\Model\Check\InvalidCheckProtocolException
     */
    public function itShouldThrowExceptionWhenUnexpectedProtocolIsGiven($protocol)
    {
        CheckUrlDataBuilder::aCheckUrl()->withProtocol($protocol)->build();
    }

    public function invalidProtocolDataProvider()
    {
        return [
            ['ftp'],
            ['ssh'],
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnAFormattedUri()
    {
        $checkUrl = CheckUrlDataBuilder::aCheckUrl()->withProtocol("https")->withDomain("test.example.com")->build();

        $this->assertEquals("https://test.example.com", $checkUrl->url());
    }

    /**
     * DISABLED for now...
     *
     * @dataProvider invalidDomainDataProvider
     * @expectedException InvalidCheckDomainException
     */
    public function itShouldThrowExceptionWithIncorrectDomains($domain)
    {
        CheckUrlDataBuilder::aCheckUrl()->withDomain($domain)->build();
    }

    public function invalidDomainDataProvider()
    {
        return [
            ["./index.html"]
        ];
    }
}
