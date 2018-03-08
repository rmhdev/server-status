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

class AlertAction
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $destination;

    public function __construct(AlertChannel $channel)
    {
        $this->type = $channel->name();
        $this->destination = $channel->destinationRaw();
    }

    public function type(): string
    {
        return $this->type;
    }

    public function destination(): string
    {
        return $this->destination;
    }

    /**
     * @return AlertChannel
     * @throws Channel\InvalidAlertChannelException
     */
    public function alertChannel(): AlertChannel
    {
        return AlertChannelFactory::create($this->type(), $this->destination());
    }
}
