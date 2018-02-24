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

class CheckTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBeCastedToString()
    {
        $check = CheckDataBuilder::aCheck()
            ->withId(CheckIdDataBuilder::aCheckId()->withValue("12345")->build())
            ->withName(CheckNameDataBuilder::aCheckName()->withSlug("check-slug")->build())
            ->build();


        $this->assertSame("12345 (check-slug)", (string) $check);
    }
}
