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

use ServerStatus\Domain\Model\Alert\AlertAction;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannel;
use ServerStatus\Tests\Domain\Model\Alert\Channel\AlertChannelEmailDataBuilder;

class AlertActionDataBuilder
{
    private $channel;

    public function __construct()
    {
        $this->channel = AlertChannelEmailDataBuilder::anAlertChannel()->build();
    }

    public function withChannel(AlertChannel $channel): AlertActionDataBuilder
    {
        $this->channel = $channel;

        return $this;
    }

    public function build(): AlertAction
    {
        return new AlertAction($this->channel);
    }

    public static function anAlertAction(): AlertActionDataBuilder
    {
        return new self();
    }
}
