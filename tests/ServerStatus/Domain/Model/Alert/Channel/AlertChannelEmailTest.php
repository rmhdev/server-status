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

class AlertChannelEmailTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveAName()
    {
        $channel = AlertChannelEmailDataBuilder::anAlertChannel()->build();

        $this->assertEquals("email", $channel->name());
    }

    /**
     * @test
     */
    public function itShouldReturnAListOfEmailsBasedOnDestinationRaw()
    {
        $channel = AlertChannelEmailDataBuilder::anAlertChannel()->withDestination(
            "user-one@example.com\t, \n user-two@example.com  "
        )->build();

        $this->assertEquals(["user-one@example.com", "user-two@example.com"], $channel->destinations());
    }
}
