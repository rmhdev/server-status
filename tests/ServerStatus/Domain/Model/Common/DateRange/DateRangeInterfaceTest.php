<?php

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Tests\Domain\Model\Common\DateRange;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeDay;

interface DateRangeInterfaceTest
{
    public function isShouldReturnCorrectFromDate();

    public function isShouldReturnCorrectToDate();

    public function itShouldReturnName();

    public function itShouldReturnTheDateFormatted();

    public function itShouldBeAbleToCastToStringWithTheFormattedDate();
}
