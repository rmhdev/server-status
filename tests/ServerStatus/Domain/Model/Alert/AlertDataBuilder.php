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
use ServerStatus\Tests\Domain\Model\Alert\Channel\AlertChannelEmailDataBuilder;
use ServerStatus\Tests\Domain\Model\Alert\Reason\AlertReasonDowntimeDataBuilder;

class AlertDataBuilder
{
    private $id;
    private $window;
    private $channel;
    private $reason;

    public function __construct()
    {
        $this->id = AlertIdDataBuilder::anAlertId()->build();
        $this->window = AlertTimeWindowDataBuilder::anAlertTimeWindow()->build();
        $this->channel = AlertChannelEmailDataBuilder::anAlertChannel()->build();
        $this->reason = AlertReasonDowntimeDataBuilder::anAlertReason()->build();
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

    public function build(): Alert
    {
        return new Alert($this->id, $this->window, $this->reason, $this->channel);
    }

    public static function anAlert(): AlertDataBuilder
    {
        return new self();
    }
}
