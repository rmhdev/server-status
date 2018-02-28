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
use ServerStatus\Domain\Model\Common\DateRange\DateRange;
use ServerStatus\Domain\Model\Measurement\MeasurementRepository;

final class MeasureSummaryFactory
{
    public static function create(
        Check $check,
        MeasurementRepository $repository,
        DateRange $dateRange
    ): MeasureSummary {

        return new MeasureSummary(
            $check,
            $repository->summaryByMinute(
                $check,
                $dateRange
            ),
            $dateRange
        );
    }
}
