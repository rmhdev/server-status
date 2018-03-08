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

namespace ServerStatus\Tests\Domain\Model\Alert;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\Alert\AlertTimeWindow;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannel;
use ServerStatus\Domain\Model\Alert\Reason\AlertReason;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Customer\Customer;
use ServerStatus\Tests\Domain\Model\Alert\Channel\AlertChannelEmailDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\Reason\AlertReasonDowntimeDataBuilder;
use ServerStatus\Tests\Domain\Model\Customer\CustomerDataBuilder;

class AlertDataBuilder
{
    private $id;
    private $window;
    private $channel;
    private $reason;
    private $customer;
    private $check;

    public function __construct()
    {
        $this->id = AlertIdDataBuilder::anAlertId()->build();
        $this->window = AlertTimeWindowDataBuilder::anAlertTimeWindow()->build();
        $this->channel = AlertChannelEmailDataBuilder::anAlertChannel()->build();
        $this->reason = AlertReasonDowntimeDataBuilder::anAlertReason()->build();
        $this->customer = CustomerDataBuilder::aCustomer()->build();
        $this->check = null;
    }

    public function withId(AlertId $id): AlertDataBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function withWindow(AlertTimeWindow $window): AlertDataBuilder
    {
        $this->window = $window;

        return $this;
    }

    public function withChannel(AlertChannel $channel): AlertDataBuilder
    {
        $this->channel = $channel;

        return $this;
    }

    public function withReason(AlertReason $reason): AlertDataBuilder
    {
        $this->reason = $reason;

        return $this;
    }

    public function withCustomer(Customer $customer): AlertDataBuilder
    {
        $this->customer = $customer;

        return $this;
    }

    public function withCheck(Check $check = null): AlertDataBuilder
    {
        $this->check = $check;

        return $this;
    }

    public function build(): Alert
    {
        return new Alert($this->id, $this->window, $this->reason, $this->channel, $this->customer, $this->check);
    }

    public static function anAlert(): AlertDataBuilder
    {
        return new self();
    }
}
