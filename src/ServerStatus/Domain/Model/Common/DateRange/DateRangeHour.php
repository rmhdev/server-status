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

final class DateRangeHour extends DateRangeAbstract implements DateRange
{
    const NAME = "hour";

    public function from(): \DateTimeImmutable
    {
        return $this->date()->setTime((int) $this->date()->format("H"), 0, 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->from()->modify("+1 hour");
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function formatted(): string
    {
        return sprintf(
            "%s, %s-%s",
            $this->from()->format("Y-m-d"),
            $this->from()->format("H:i"),
            $this->to()->format("H:i")
        );
    }

    /**
     * @inheritdoc
     */
    public function interval(): \DateInterval
    {
        return new \DateInterval("PT1H");
    }

    protected function createDateRange(\DateTimeImmutable $date): DateRange
    {
        return new DateRangeCustom($date, $date->add(new \DateInterval("PT600S")));
    }
}
