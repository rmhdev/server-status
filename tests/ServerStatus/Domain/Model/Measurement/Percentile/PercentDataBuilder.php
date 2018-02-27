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

namespace ServerStatus\Tests\Domain\Model\Measurement\Percentile;

use ServerStatus\Domain\Model\Measurement\Percentile\Percent;

final class PercentDataBuilder
{
    /**
     * @var float
     */
    private $value;

    public function __construct()
    {
        $this->value = 0;
    }

    public function withValue($value): PercentDataBuilder
    {
        $this->value = $value;

        return $this;
    }

    public function build()
    {
        return new Percent($this->value);
    }

    public static function aPercent(): PercentDataBuilder
    {
        return new self();
    }
}
