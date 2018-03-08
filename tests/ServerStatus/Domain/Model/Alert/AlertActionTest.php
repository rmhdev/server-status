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

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannel;
use ServerStatus\Tests\Domain\Model\Alert\Channel\AlertChannelEmailDataBuilder;

class AlertActionTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnBasicValues()
    {
        $action = AlertActionDataBuilder::anAlertAction()->withChannel(
            AlertChannelEmailDataBuilder::anAlertChannel()->withDestination("username@example.com")->build()
        )->build();

        $this->assertEquals("email", $action->type());
        $this->assertEquals("username@example.com", $action->destination());
    }

    /**
     * @test
     */
    public function itShouldReturnAnAlertChannel()
    {
        $action = AlertActionDataBuilder::anAlertAction()->build();

        $this->assertInstanceOf(AlertChannel::class, $action->alertChannel());
    }
}
