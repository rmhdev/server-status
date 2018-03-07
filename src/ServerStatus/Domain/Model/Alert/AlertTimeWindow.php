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

namespace ServerStatus\Domain\Model\Alert;

use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeMinutes;

class AlertTimeWindow
{
    const MIN_MINUTES = 5;
    const MAX_MINUTES = 60;

    /**
     * @var integer
     */
    private $minutes;

    public function __construct(int $minutes = self::MIN_MINUTES)
    {
        $this->assertMinutes($minutes);
        $this->minutes = $minutes;
    }

    private function assertMinutes(int $minutes)
    {
        if (!in_array($minutes, self::availableMinutes())) {
            throw new \UnexpectedValueException(sprintf(
                'Value %s is not available, select between [%s]',
                $minutes,
                implode(",", self::availableMinutes())
            ));
        }
    }

    public function minutes(): int
    {
        return $this->minutes;
    }

    public function dateRange(\DateTimeInterface $dateTime): DateRange
    {
        $date = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $dateTime->format(DATE_ISO8601));
        $minutes = ((int) $date->format("i"));
        $roundedMinutes = $minutes - ($minutes % $this->minutes());

        return new DateRangeMinutes(
            $date->setTime((int) $date->format("H"), $roundedMinutes, 0),
            $this->minutes()
        );
    }

    public function __toString(): string
    {
        return (string) $this->minutes;
    }

    /**
     * @return int[]
     */
    public static function availableMinutes(): array
    {
        return [5, 10, 15, 30, 60];
    }
}
