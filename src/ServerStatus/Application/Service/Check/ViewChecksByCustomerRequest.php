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

namespace ServerStatus\Application\Service\Check;

use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeFactory;
use ServerStatus\Domain\Model\Common\DateRange\DateRangeLast24Hours;
use ServerStatus\Domain\Model\Customer\CustomerId;

class ViewChecksByCustomerRequest
{
    private $customerId;
    private $date;
    private $dateRange;

    public function __construct(
        CustomerId $customerId,
        \DateTimeInterface $dateTime = null,
        string $name = DateRangeLast24Hours::NAME
    ) {
        $date = $dateTime ? $dateTime : new \DateTime("now");
        $this->date = $date->format(DATE_ISO8601);
        $this->dateRange = DateRangeFactory::create($name, $date);
        $this->customerId = $customerId;
    }

    public function customerId(): CustomerId
    {
        return $this->customerId;
    }

    /**
     * @deprecated
     * @return \DateTimeImmutable
     */
    public function date(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->date);
    }

    public function dateRange(): DateRange
    {
        return $this->dateRange;
    }

    /**
     * @deprecated
     * @return string
     */
    public function name(): string
    {
        return $this->dateRange()->name();
    }
}
