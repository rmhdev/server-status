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

final class DateRangeHours extends DateRangeAbstract implements DateRange
{
    const NAME = "hours";

    private $hours;

    public function __construct(\DateTimeInterface $dateTime, int $hours = 1)
    {
        $this->assertHours($hours);
        parent::__construct($dateTime);
        $this->hours = $hours;
    }

    private function assertHours(int $hours)
    {
        if (1 > $hours) {
            throw new \UnexpectedValueException(sprintf(
                "Hours must be greater than zero, %d received",
                $hours
            ));
        }
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date()->setTime((int) $this->date()->format("H"), 0, 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->from()->modify(sprintf("+%d hours", $this->hours));
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

    protected function createDateRange(\DateTimeImmutable $date): DateRange
    {
        return new DateRangeMinutes($date);
    }
}
