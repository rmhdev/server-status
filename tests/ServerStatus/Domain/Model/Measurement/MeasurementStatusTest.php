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

class MeasurementStatusTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBeSuccessfulWhenResponseCodeIsCorrect()
    {
        $result = $this->createWithCode(200);

        $this->assertTrue($result->isSuccessful());
        $this->assertFalse($result->isInformational());
        $this->assertFalse($result->isRedirection());
        $this->assertFalse($result->isClientError());
        $this->assertFalse($result->isServerError());
    }

    /**
     * @test
     */
    public function itShouldReturnTheStatusCodeName()
    {
        $this->assertEquals("informational", $this->createWithCode(100)->statusName());
        $this->assertEquals("successful", $this->createWithCode(200)->statusName());
        $this->assertEquals("redirection", $this->createWithCode(300)->statusName());
        $this->assertEquals("client_error", $this->createWithCode(400)->statusName());
        $this->assertEquals("server_error", $this->createWithCode(500)->statusName());
    }

    private function createWithCode($code)
    {
        return MeasurementStatusDataBuilder::aMeasurementStatus()->withCode($code)->build();
    }

    /**
     * @test
     */
    public function itShouldReturnTrueIfIsInternalError()
    {
        $result = $this->createWithCode(0);

        $this->assertTrue($result->isInternalError());
        $this->assertEquals('error', $result->statusName());
    }

    /**
     * @test
     */
    public function itShouldSayIfOtherMeasurementIsEqualByCode()
    {
        $result = $this->createWithCode(200);

        $this->assertTrue($result->equals($this->createWithCode(200)));
        $this->assertFalse($result->equals($this->createWithCode(201)));
    }

    /**
     * @test
     */
    public function itShouldBeComparableUsingTheCode()
    {
        $result = $this->createWithCode(200);

        $this->assertSame(0, $result->compareTo($this->createWithCode(200)));
        $this->assertSame(-1, $result->compareTo($this->createWithCode(201)));
        $this->assertSame(1, $result->compareTo($this->createWithCode(199)));
    }

    /**
     * @test
     */
    public function itShouldReturnTheClassResponse()
    {
        $this->assertSame(1, $this->createWithCode(100)->classResponse());
        $this->assertSame(2, $this->createWithCode(200)->classResponse());
        $this->assertSame(3, $this->createWithCode(301)->classResponse());
    }

    /**
     * @test
     */
    public function itShouldReturnIfAStatusHasSameClassResponse()
    {
        $result = $this->createWithCode(200);

        $this->assertTrue($result->hasSameClassResponse($this->createWithCode(200)));
        $this->assertTrue($result->hasSameClassResponse($this->createWithCode(201)));
        $this->assertFalse($result->hasSameClassResponse($this->createWithCode(301)));
    }
}
