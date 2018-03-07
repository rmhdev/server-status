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

final class DateRangeMinutes extends DateRangeAbstract implements DateRange
{
    const NAME = "minutes";

    private $minutes;

    public function __construct(\DateTimeInterface $dateTime, int $minutes = 10)
    {
        $this->assertMinutes($minutes);
        parent::__construct($dateTime);
        $this->minutes = $minutes;
    }

    private function assertMinutes(int $minutes)
    {
        if (1 > $minutes) {
            throw new \UnexpectedValueException(sprintf(
                "Minutes must be greater than zero, %d received",
                $minutes
            ));
        }
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date()->setTime((int) $this->date()->format("H"), (int) $this->date()->format("i"), 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->from()->modify(sprintf("+%s minutes", $this->minutes));
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
        return new DateRangeMinutes($date, 1);
    }
}
