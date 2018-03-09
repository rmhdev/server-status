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
use ServerStatus\Domain\Model\Check\CheckStatus;

class CheckStatusTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBeEnabledByDefault()
    {
        $status = CheckStatusDataBuilder::aCheckStatus()->withCode("")->build();

        $this->assertTrue($status->isEnabled());
    }

    /**
     * @test
     */
    public function itShouldBeDisabledWhenDefined()
    {
        $status = CheckStatusDataBuilder::aCheckStatus()->withCode(CheckStatus::CODE_DISABLED)->build();

        $this->assertFalse($status->isEnabled());
    }

    /**
     * @test
     * @expectedException \ServerStatus\Domain\Model\Check\InvalidCheckStatusException
     */
    public function itShouldThrowExceptionWhenIncorrectCodeIsUsed()
    {
        CheckStatusDataBuilder::aCheckStatus()->withCode("lorem")->build();
    }
}
