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

namespace ServerStatus\Domain\Model\Measurement\Performance;

use ServerStatus\Domain\Model\Check\Check;

class PerformanceReportWeek implements PerformanceReport
{
    private $check;
    private $date;
    private $performance;

    public function __construct(Check $check, \DateTimeInterface $dateTime, Performance $performance)
    {
        $this->check = $check;
        $this->date = $dateTime->format(DATE_ISO8601);
        $this->performance = $performance;
    }

    public function name(): string
    {
        return "week";
    }

    public function check(): Check
    {
        return $this->check;
    }

    protected function date(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(DATE_ISO8601, $this->date);
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date()->modify("Monday this week")->setTime(0, 0, 0, 0);
    }

    public function to(): \DateTimeImmutable
    {
        return $this->date()->modify("Monday next week")->setTime(0, 0, 0, 0);
    }

    public function performance(): Performance
    {
        return $this->performance;
    }
}
