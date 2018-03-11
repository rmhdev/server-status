<?php
declare(strict_types=1);

/**
 * This file is part of the bidaia package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Infrastructure\Domain\Model\Alert;

use ServerStatus\Domain\Model\Alert\Alert;
use ServerStatus\Domain\Model\Alert\AlertFactory;
use ServerStatus\Domain\Model\Alert\AlertId;
use ServerStatus\Domain\Model\Alert\AlertTimeWindow;
use ServerStatus\Domain\Model\Alert\Channel\AlertChannel;
use ServerStatus\Domain\Model\Alert\Reason\AlertReason;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Customer\Customer;

class DoctrineAlertFactory implements AlertFactory
{
    public function build(
        AlertId $id,
        AlertTimeWindow $window,
        AlertReason $reason,
        AlertChannel $channel,
        Customer $customer,
        Check $check = null
    ): Alert {
        return new Alert($id, $window, $reason, $channel, $customer, $check);
    }
}
