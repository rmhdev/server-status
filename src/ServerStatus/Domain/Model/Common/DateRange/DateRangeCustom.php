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

final class DateRangeCustom extends DateRangeAbstract implements DateRange
{
    const NAME = "custom";

    private $to;

    public function __construct(\DateTimeInterface $from, \DateTimeInterface $to)
    {
        $this->assertDatesHaveSameTimezone($from, $to);
        $this->assertDatesDiff($from, $to);
        $this->to = $this->createImmutableCopy($to);
        parent::__construct($from);
    }

    private function assertDatesHaveSameTimezone(\DateTimeInterface $from, \DateTimeInterface $to)
    {
        if ($from->getTimezone()->getName() != $to->getTimezone()->getName()) {
            throw new \UnexpectedValueException(sprintf(
                'Dates have differente timezones, from is "%s" and to is "%s"',
                $from->getTimezone()->getName(),
                $to->getTimezone()->getName()
            ));
        }
    }

    private function assertDatesDiff(\DateTimeInterface $from, \DateTimeInterface $to)
    {
        if ($from >= $to) {
            throw new \UnexpectedValueException(sprintf(
                'date "from" ("%s") is greater than "to" ("%s")',
                $from->format(DATE_ISO8601),
                $to->format(DATE_ISO8601)
            ));
        }
    }

    public function from(): \DateTimeImmutable
    {
        return $this->date();
    }

    public function to(): \DateTimeImmutable
    {
        return $this->to;
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function formatted(): string
    {
        return sprintf(
            "%s, %s",
            $this->from()->format("Y-m-d H:i:s"),
            $this->to()->format("Y-m-d H:i:s")
        );
    }

    /**
     * we need to force to select the next custom range
     * @return DateRange
     */
    public function next(): DateRange
    {
        return new self($this->to(), $this->to()->add($this->diff()));
    }

    private function diff(): \DateInterval
    {
        return $this->from()->diff($this->to());
    }

    /**
     * we need to force to select the previous custom range
     * @return DateRange
     */
    public function previous(): DateRange
    {
        return new self($this->from()->sub($this->diff()), $this->from());
    }

    /**
     * @inheritdoc
     */
    public function interval(): \DateInterval
    {
        return new \DateInterval("PT600S");
    }
}
