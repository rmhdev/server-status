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

namespace ServerStatus\Domain\Model\Alert\Reason;

use ServerStatus\Domain\Model\Measurement\MeasurementResult;

final class AlertReasonDowntime implements AlertReason
{
    const NAME = "downtime";

    public function name(): string
    {
        return self::NAME;
    }

    public function isAvailable(MeasurementResult $result): bool
    {
        return
            $result->status()->isServerError() ||
            $result->status()->isClientError();
    }

    public function __toString(): string
    {
        return sprintf('a %s', $this->name());
    }
}
