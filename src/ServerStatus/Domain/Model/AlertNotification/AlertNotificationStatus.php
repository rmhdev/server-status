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

namespace ServerStatus\Domain\Model\AlertNotification;

class AlertNotificationStatus
{
    const READY = "ready";
    const SENDING = "sending";
    const SENT = "sent";
    const ERROR = "error";

    /**
     * @var string
     */
    private $code;


    public function __construct(string $code = "")
    {
        $processed = $this->processCode($code);
        $this->assertCode($processed);
        $this->code = $processed;
    }

    private function processCode(string $code = "")
    {
        if ("" === $code) {
            return self::READY;
        }

        return $code;
    }

    private function assertCode(string $code)
    {
        if (!in_array($code, self::codes())) {
            throw new InvalidAlertNotificationStatusException(
                sprintf('Code "%s" is invalid', $code)
            );
        }
    }

    public function code(): string
    {
        return $this->code;
    }

    public function isCode($code): bool
    {
        return $this->code === $code;
    }

    /**
     * @return string[]
     */
    public static function codes()
    {
        return array_merge(self::successCodes(), self::errorCodes());
    }

    /**
     * @return string[]
     */
    public static function successCodes()
    {
        return [
            self::READY,
            self::SENDING,
            self::SENT,
        ];
    }

    /**
     * @return string[]
     */
    public static function errorCodes()
    {
        return [
            self::ERROR
        ];
    }
}
