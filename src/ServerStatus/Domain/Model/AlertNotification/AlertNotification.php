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

namespace ServerStatus\Domain\Model\AlertNotification;

use ServerStatus\Domain\Model\Check\Check;

class AlertNotification
{
    /**
     * @var Check
     */
    private $check;

    /**
     * @var \DateTime
     */
    private $dateTime;


    public function __construct(Check $check, \DateTimeInterface $dateTime)
    {
        $this->check = $check;
        $this->dateTime = $this->createDateTime($dateTime);
    }

    private function createDateTime(\DateTimeInterface $dateTime)
    {
        return \DateTime::createFromFormat(
            DATE_ISO8601,
            $dateTime->format(DATE_ISO8601),
            $dateTime->getTimezone()
        );
    }

    public function check(): Check
    {
        return $this->check;
    }

    public function dateTime(): \DateTime
    {
        return $this->dateTime;
    }
}
