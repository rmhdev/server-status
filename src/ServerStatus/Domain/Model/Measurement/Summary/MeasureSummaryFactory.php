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
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

final class MeasureSummaryFactory
{
    public static function create(
        string $name,
        Check $check,
        MeasurementRepository $repository,
        \DateTimeInterface $date = null
    ): MeasureSummary {
        switch ($name) {
            case MeasureLast24HoursSummary::NAME:
                $base = new MeasureLast24HoursSummary($check, [], $date);

                return new MeasureLast24HoursSummary(
                    $check,
                    $repository->summaryByMinute(
                        $check,
                        $base->from(),
                        $base->to()
                    ),
                    $date
                );
            case MeasureDaySummary::NAME:
                $base = new MeasureDaySummary($check, [], $date);

                return new MeasureDaySummary(
                    $check,
                    $repository->summaryByMinute(
                        $check,
                        $base->from(),
                        $base->to()
                    ),
                    $date
                );
            default:
                throw new \UnexpectedValueException(sprintf(
                    'Unknown MeasureSummary name "%s"',
                    $name
                ));
        }
    }
}
