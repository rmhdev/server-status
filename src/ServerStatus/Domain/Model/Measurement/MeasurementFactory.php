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

namespace ServerStatus\Domain\Model\Measurement;

use ServerStatus\Domain\Model\Check\Check;

interface MeasurementFactory
{
    public function build(
        MeasurementId $id,
        \DateTimeInterface $dateCreated,
        Check $check,
        MeasurementResult $result
    ): Measurement;
}
