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

final class MeasureSummaryFactory
{
    public static function create(string $name, $values = [], \DateTimeInterface $date = null): MeasureSummary
    {
        switch ($name) {
            case MeasureLast24HoursSummary::NAME:
                return new MeasureLast24HoursSummary($values, $date);
            case MeasureDaySummary::NAME:
                return new MeasureDaySummary($values, $date);
            default:
                throw new \UnexpectedValueException(sprintf(
                    'Unknown MeasureSummary name "%s"',
                    $name
                ));
        }
    }
}
