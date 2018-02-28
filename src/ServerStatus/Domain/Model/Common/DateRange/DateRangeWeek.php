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

namespace ServerStatus\Domain\Model\Common\DateRange;

final class DateRangeWeek extends DateRangeAbstract implements DateRange
{
    public function from(): \DateTimeImmutable
    {
        return $this->date()->modify("monday this week")->setTime(0, 0, 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->from()->modify("+1 week");
    }

    public function name(): string
    {
        return "week";
    }

    public function formatted(): string
    {
        return sprintf("%s, week %d", $this->from()->format("o"), $this->from()->format("W"));
    }
}
