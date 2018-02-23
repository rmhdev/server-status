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

namespace ServerStatus\Domain\Model\Measurement\Performance;

use ServerStatus\Domain\Model\Check\Check;

interface PerformanceReport
{
    public function check(): Check;

    public function name(): string;

    public function from(): \DateTimeImmutable;

    public function to(): \DateTimeImmutable;

    public function performance(): Performance;
}
