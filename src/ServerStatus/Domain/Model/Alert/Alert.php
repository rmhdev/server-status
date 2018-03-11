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

use ServerStatus\Domain\Model\Alert\Channel\AlertChannel;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannelFactory;
use ServerStatus\Domain\Model\Alert\Reason\AlertReason;
use ServerStatus\Domain\Model\Alert\Reason\AlertReasonFactory;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Check\InvalidCheckException;
use ServerStatus\Domain\Model\Customer\Customer;

class Alert
{
    /**
     * @var AlertId
     */
    private $id;

    /**
     * @var AlertTimeWindow
     */
    private $window;

    private $reasonName;

    /**
     * @var string
     */
    private $channelName;

    /**
     * @var string
     */
    private $channelDestinationRaw;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Check|null
     */
    private $check;


    public function __construct(
        AlertId $id,
        AlertTimeWindow $window,
        AlertReason $reason,
        AlertChannel $channel,
        Customer $customer,
        Check $check = null
    ) {
        $this->assertCheckIsRelatedToCustomer($customer, $check);
        $this->id = $id;
        $this->window = $window;
        $this->reasonName = $reason->name();
        $this->channelName = $channel->name();
        $this->channelDestinationRaw = $channel->destinationRaw();
        $this->customer = $customer;
        $this->check = $check;
    }

    private function assertCheckIsRelatedToCustomer(Customer $customer, Check $check = null)
    {
        if (is_null($check)) {
            return;
        }
        if (!$check->customer()->id()->equals($customer->id())) {
            throw new InvalidCheckException(sprintf(
                'Check "%s" by customer "%s" is not related to customer "%s"',
                $check,
                $check->customer(),
                $customer
            ));
        }
    }

    public function id(): AlertId
    {
        return $this->id;
    }

    public function timeWindow(): AlertTimeWindow
    {
        return $this->window;
    }

    public function reason(): AlertReason
    {
        return AlertReasonFactory::create($this->reasonName);
    }

    public function channel(): AlertChannel
    {
        return AlertChannelFactory::create($this->channelName, $this->channelDestinationRaw);
    }

    public function customer(): Customer
    {
        return $this->customer;
    }

    public function check(): ?Check
    {
        return $this->check;
    }

    public function isEnabled(): bool
    {
        if (is_null($this->check())) {
            return $this->customer()->status()->isEnabled();
        }

        return $this->customer()->status()->isEnabled() &&
            $this->check()->status()->isEnabled();
    }

    public function __toString(): string
    {
        $checksText = sprintf('any check by customer %s', $this->customer());
        if ($this->check()) {
            $checksText = sprintf('check %s by customer %s', $this->check(), $this->customer());
        }

        return sprintf(
            'If %s has happened in the %s to %s, then send an alert via %s',
            $this->reason(),
            $this->timeWindow(),
            $checksText,
            $this->channel()
        );
    }
}
