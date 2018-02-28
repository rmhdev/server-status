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

final class DateRangeLast24Hours extends DateRangeAbstract implements DateRange
{
    const NAME = "last_24_hours";

    public function from(): \DateTimeImmutable
    {
        return $this->to()->modify("-24 hours");
    }

    public function to(): \DateTimeImmutable
    {
        return $this->date()->setTime((int) $this->date()->format("H"), 0, 0, 0);
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function formatted(): string
    {
        return sprintf(
            "%s, %s",
            $this->from()->format("Y-m-d H:i"),
            $this->to()->format("Y-m-d H:i")
        );
    }

    /**
     * 24 hours is a custom range, that's why we need to force to select the next day
     * @return DateRange
     */
    public function next(): DateRange
    {
        return new self($this->to()->modify("+1 day"));
    }

    /**
     * 24 hours is a custom range, that's why we need to force to select the previous day
     * @return DateRange
     */
    public function previous(): DateRange
    {
        return new self($this->from()->modify("-1 day"));
    }
}
