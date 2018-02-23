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

use ServerStatus\Domain\Model\Measurement\Summary\MeasureLast24HoursSummary;
use ServerStatus\Domain\Model\Customer\CustomerId;

class ViewChecksByCustomerRequest
{
    private $customerId;
    private $date;
    private $name;

    public function __construct(CustomerId $customerId, \DateTimeInterface $dateTime = null, string $name = "")
    {
        $date = $dateTime ? $dateTime : new \DateTime("now");
        $this->date = $date->format(DATE_ISO8601);
        $this->customerId = $customerId;
        $this->name = strlen($name) ? $name : MeasureLast24HoursSummary::NAME;
    }

    public function customerId(): CustomerId
    {
        return $this->customerId;
    }

    public function date(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->date);
    }

    public function name(): string
    {
        return $this->name;
    }
}
