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

abstract class DateRangeAbstract implements DateRange
{
    /**
     * @var \DateTimeImmutable
     */
    private $date;

    public function __construct(\DateTimeInterface $dateTime)
    {
        $this->date = \DateTimeImmutable::createFromFormat(DATE_ISO8601, $dateTime->format(DATE_ISO8601));
    }

    protected function date(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function __toString(): string
    {
        return $this->formatted();
    }
}
