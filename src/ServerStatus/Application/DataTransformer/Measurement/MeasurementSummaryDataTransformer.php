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

namespace ServerStatus\Application\DataTransformer\Measurement;

use ServerStatus\Domain\Model\Measurement\Summary\MeasureSummary;

interface MeasurementSummaryDataTransformer
{
    public function write(MeasureSummary $measureSummary);

    public function read();
}
