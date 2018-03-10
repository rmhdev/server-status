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
    const NAME = "week";
    const INTERVAL_HOURS = 1;

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
        return self::NAME;
    }

    public function formatted(): string
    {
        if ($this->from()->format("M") === $this->to()->format("M")) {
            return sprintf(
                "%s W%d: %s %s..%s",
                $this->from()->format("o"),
                $this->from()->format("W"),
                $this->from()->format("M"),
                $this->from()->format("d"),
                $this->to()->format("d")
            );
        }
        return sprintf(
            "%s W%d: %s, %s",
            $this->from()->format("o"),
            $this->from()->format("W"),
            $this->from()->format("M d"),
            $this->to()->format("M d")
        );
    }

    protected function createDateRange(\DateTimeImmutable $date): DateRange
    {
        return new DateRangeHours($date, self::INTERVAL_HOURS);
    }
}
