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

final class MeasurementSummaryDtoDataTransformer implements MeasurementSummaryDataTransformer
{
    private $measurements;

    public function write($measurements = [])
    {
        $this->measurements = $measurements;
    }

    public function read()
    {
        $data = [];


        return $data;
    }
}
