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

final class MeasurementCollection implements \Countable
{
    private $measurements;

    public function __construct($measurements = [])
    {
        $this->measurements = $measurements;
    }

    public function count(): int
    {
        return 0;
    }
}
