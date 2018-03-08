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

namespace ServerStatus\Domain\Model\Alert\Channel;

final class AlertChannelFactory
{
    private static $channels = [
        AlertChannelEmail::class,
    ];

    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_map(function ($class) {
            return $class::NAME;
        }, self::$channels);
    }

    /**
     * @param string $name
     * @param string $destination
     * @return AlertChannel
     * @throws InvalidAlertChannelException
     */
    public static function create(string $name, string $destination): AlertChannel
    {
        if ("" === $name) {
            throw new InvalidAlertChannelException(
                'You must indicate the name of the AlertChannel, empty value received'
            );
        }
        foreach (self::$channels as $dateRangeClass) {
            if ($name === $dateRangeClass::NAME) {
                return new $dateRangeClass($destination);
            }
        }

        throw new InvalidAlertChannelException(
            sprintf('AlertChannel name "%s" is unknown', $name)
        );
    }
}
