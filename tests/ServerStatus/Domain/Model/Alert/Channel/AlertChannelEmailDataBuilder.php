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

namespace ServerStatus\Tests\Domain\Model\Alert\Channel;

use ServerStatus\Domain\Model\Alert\Channel\AlertChannel;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannelEmail;

class AlertChannelEmailDataBuilder
{
    private $destination;

    public function __construct()
    {
        $this->destination = "";
    }

    public function withDestination($destination): AlertChannelEmailDataBuilder
    {
        $this->destination = $destination;

        return $this;
    }

    public function build(): AlertChannel
    {
        return new AlertChannelEmail($this->destination);
    }

    public static function anAlertChannel(): AlertChannelEmailDataBuilder
    {
        return new self();
    }
}
