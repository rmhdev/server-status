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
use ServerStatus\Domain\Model\Common\DateRange\DateRange;

final class PerformanceReport
{
    /**
     * @var Check
     */
    private $check;

    /**
     * @var DateRange
     */
    private $dateRange;

    /**
     * @var Performance
     */
    private $performance;

    public function __construct(Check $check, DateRange $dateRange, Performance $performance)
    {
        $this->check = $check;
        $this->dateRange = $dateRange;
        $this->performance = $performance;
    }

    public function check(): Check
    {
        return $this->check;
    }

    public function dateRange(): DateRange
    {
        return $this->dateRange;
    }

    public function performance(): Performance
    {
        return $this->performance;
    }
}
