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

interface MeasurementSummaryDataTransformer
{
    public function write($measurements = []);

    public function read();
}