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
        return "last_24_hours";
    }

    public function formatted(): string
    {
        return sprintf(
            "%s, %s",
            $this->from()->format("Y-m-d H:i"),
            $this->to()->format("Y-m-d H:i")
        );
    }
}
