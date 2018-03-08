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

namespace ServerStatus\Domain\Model\Alert\Reason;

final class AlertReasonFactory
{
    private static $reasons = [
        AlertReasonDowntime::class,
    ];

    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_map(function ($class) {
            return $class::NAME;
        }, self::$reasons);
    }

    /**
     * @param string $name
     * @return AlertReason
     * @throws InvalidAlertReasonException
     */
    public static function create(string $name): AlertReason
    {
        if ("" === $name) {
            throw new InvalidAlertReasonException(
                'You must indicate the name of the reason, empty value received'
            );
        }
        foreach (self::$reasons as $reasonClass) {
            if ($name === $reasonClass::NAME) {
                return new $reasonClass();
            }
        }

        throw new InvalidAlertReasonException(
            sprintf('Alert reason name "%s" is unknown', $name)
        );
    }
}
