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

    /**
     * @test
     */
    public function itShouldBeAbleToBeConvertedToString()
    {
        $builder = AlertChannelEmailDataBuilder::anAlertChannel();
        $this->assertSame(
            "email to user-one@example.com",
            (string) $builder->withDestination("user-one@example.com")->build()
        );

        $this->assertSame(
            "email to user-one@example.com and user-two@example.com",
            (string) $builder->withDestination("user-one@example.com,user-two@example.com")->build()
        );

        $this->assertSame(
            "email to a@example.com, b@example.com and 1 more address",
            (string) $builder->withDestination("a@example.com,b@example.com,c@example.com")->build()
        );

        $this->assertSame(
            "email to a@example.com, b@example.com and 2 more addresses",
            (string) $builder->withDestination("a@example.com,b@example.com,c@example.com,d@example.com")->build()
        );
    }
}
