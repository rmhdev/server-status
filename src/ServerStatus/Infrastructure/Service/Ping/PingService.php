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

namespace ServerStatus\Infrastructure\Service\Ping;

use ServerStatus\Domain\Model\Check\CheckUrl;
use ServerStatus\Domain\Model\Measurement\MeasurementResult;

interface PingService
{
    public function measure(CheckUrl $checkUrl): MeasurementResult;
}
