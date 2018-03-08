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


    public function __construct(AlertId $id, AlertTimeWindow $window, AlertReason $reason, AlertChannel $channel)
    {
        $this->id = $id;
        $this->window = $window;
        $this->reasonName = $reason->name();
        $this->channelName = $channel->name();
        $this->channelDestinationRaw = $channel->destinationRaw();
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

    public function __toString(): string
    {
        return sprintf(
            'If %s has happened in the %s, then send an alert via %s',
            $this->reason(),
            $this->timeWindow(),
            $this->channel()
        );
    }
}
