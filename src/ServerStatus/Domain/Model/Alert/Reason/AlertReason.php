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

interface AlertReason
{
    public function name(): string;

    public function isAvailable(MeasurementResult $result): bool;

    public function __toString(): string ;
}
