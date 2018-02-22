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

namespace ServerStatus\Domain\Model\Measurement\Summary;

use ServerStatus\Domain\Model\Check\Check;

interface MeasureSummary
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return Check
     */
    public function check(): Check;

    /**
     * minimum datetime
     *
     * @return \DateTimeImmutable
     */
    public function from(): \DateTimeImmutable;

    /**
     * maximum datetime
     *
     * @return \DateTimeImmutable
     */
    public function to(): \DateTimeImmutable;

    /**
     * Average values are grouped every X minutes
     *
     * @return int
     */
    public function groupedByMinutes();

    /**
     * All related SummaryAverages
     *
     * @return SummaryAverage[]
     */
    public function averages(): array;

    /**
     * Single summaryAverage
     *
     * @param \DateTimeInterface $fromDate
     * @return SummaryAverage
     */
    public function average(\DateTimeInterface $fromDate): SummaryAverage;
}
