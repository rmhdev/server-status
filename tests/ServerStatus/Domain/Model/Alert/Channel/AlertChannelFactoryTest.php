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

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannelEmail;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannelFactory;

class AlertChannelFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider availableChannels
     */
    public function itShouldCreateAvailableChannels($channelName, $className, $destination)
    {
        $channel = AlertChannelFactory::create($channelName, $destination);

        $this->assertInstanceOf($className, $channel);
    }

    public function availableChannels()
    {
        return [
            [AlertChannelEmail::NAME, AlertChannelEmail::class, "test@example.com"],
        ];
    }

    /**
     * @test
     * @dataProvider incorrectChannels
     * @expectedException \ServerStatus\Domain\Model\Alert\Channel\InvalidAlertChannelException
     */
    public function itShouldThrowExceptionWhenCreatingAnIncorrectChannel($channelName, $destination)
    {
        AlertChannelFactory::create($channelName, $destination);
    }

    public function incorrectChannels()
    {
        return [
            ["", "destination"],
            ["incorrect-channel", "destination"],
        ];
    }
}
