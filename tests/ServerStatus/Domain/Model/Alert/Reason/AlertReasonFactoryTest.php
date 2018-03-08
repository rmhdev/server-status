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

namespace ServerStatus\Tests\Domain\Model\Alert\Reason;

use PHPUnit\Framework\TestCase;
use ServerStatus\Domain\Model\Alert\Reason\AlertReasonDowntime;
use ServerStatus\Domain\Model\Alert\Reason\AlertReasonFactory;

class AlertReasonFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider availableReasons
     */
    public function itShouldCreateAvailableReason($name, $className)
    {
        $reason = AlertReasonFactory::create($name);

        $this->assertInstanceOf($className, $reason);
    }

    public function availableReasons()
    {
        return [
            [AlertReasonDowntime::NAME, AlertReasonDowntime::class],
        ];
    }

    /**
     * @test
     * @dataProvider incorrectReasons
     * @expectedException \ServerStatus\Domain\Model\Alert\Reason\InvalidAlertReasonException
     */
    public function itShouldThrowExceptionWhenCreatingAnIncorrectReason($name)
    {
        AlertReasonFactory::create($name);
    }

    public function incorrectReasons()
    {
        return [
            [""],
            ["incorrect-reason"],
        ];
    }
}
