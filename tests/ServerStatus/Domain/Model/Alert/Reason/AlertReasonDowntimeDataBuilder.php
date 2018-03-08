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

use ServerStatus\Domain\Model\Alert\Reason\AlertReason;
use ServerStatus\Domain\Model\Alert\Reason\AlertReasonDowntime;

final class AlertReasonDowntimeDataBuilder
{
    public function build(): AlertReason
    {
        return new AlertReasonDowntime();
    }

    public static function anAlertReason(): AlertReasonDowntimeDataBuilder
    {
        return new self();
    }
}
