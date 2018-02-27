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

namespace ServerStatus\Domain\Model\Measurement\Percentile;

final class Percentile
{
    /**
     * @var Percent
     */
    private $percent;

    /**
     * @var float
     */
    private $value;


    public function __construct(Percent $percent, float $value)
    {
        $this->percent = $percent;
        $this->value = $value;
    }

    public function percent(): Percent
    {
        return $this->percent;
    }

    public function value(): float
    {
        return $this->value;
    }
}
