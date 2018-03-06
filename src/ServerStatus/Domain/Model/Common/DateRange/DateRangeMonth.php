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

final class DateRangeMonth extends DateRangeAbstract implements DateRange
{
    const NAME = "month";

    public function from(): \DateTimeImmutable
    {
        return $this->date()->modify("first day of this month")->setTime(0, 0, 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->from()->modify("+1 month");
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function formatted(): string
    {
        return $this->from()->format("Y-m");
    }

    /**
     * @inheritdoc
     */
    public function interval(): \DateInterval
    {
        return new \DateInterval("PT4H");
    }
}
