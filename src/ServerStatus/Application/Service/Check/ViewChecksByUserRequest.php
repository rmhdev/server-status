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
use ServerStatus\ServerStatus\Domain\Model\User\User;

class ViewChecksByUserRequest
{
    private $user;
    private $date;
    private $name;

    public function __construct(User $user, \DateTimeInterface $dateTime = null, string $name = "")
    {
        $date = $dateTime ? $dateTime : new \DateTime("now");
        $this->date = $date->format(DATE_ISO8601);
        $this->user = $user;
        $this->name = strlen($name) ? $name : MeasureLast24HoursSummary::NAME;
    }

    public function user(): User
    {
        return $this->user;
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
