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

namespace ServerStatus\Domain\Model\Measurement;

class MeasurementStatus
{
    const STATUS_CODE_CLASS_INFORMATIONAL = 1;
    const STATUS_CODE_CLASS_SUCCESSFUL = 2;
    const STATUS_CODE_CLASS_REDIRECTION = 3;
    const STATUS_CODE_CLASS_CLIENT_ERROR = 4;
    const STATUS_CODE_CLASS_SERVER_ERROR = 5;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $reasonPhrase;


    /**
     * @param integer $statusCode The response status Code
     * @param string $reasonPhrase The response reason phrase
     */
    public function __construct(int $statusCode, string $reasonPhrase = "")
    {
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @return int The response status code
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function reasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function isInformational(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_INFORMATIONAL);
    }

    public function isSuccessful(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_SUCCESSFUL);
    }

    public function isRedirection(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_REDIRECTION);
    }

    public function isClientError(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_CLIENT_ERROR);
    }

    public function isServerError(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_SERVER_ERROR);
    }

    public function isInternalError(): bool
    {
        return 0 == $this->statusCodeClass();
    }

    public function statusName(): string
    {
        switch ($this->statusCodeClass()) {
            case self::STATUS_CODE_CLASS_INFORMATIONAL:
                return "informational";
            case self::STATUS_CODE_CLASS_SUCCESSFUL:
                return "successful";
            case self::STATUS_CODE_CLASS_REDIRECTION:
                return "redirection";
            case self::STATUS_CODE_CLASS_CLIENT_ERROR:
                return "client_error";
            case self::STATUS_CODE_CLASS_SERVER_ERROR:
                return "server_error";
            default:
                return "error";
        }
    }

    private function isStatusCodeClass(int $classNumber): bool
    {
        return $classNumber === $this->statusCodeClass();
    }

    private function statusCodeClass(): int
    {
        return (int) substr((string) $this->statusCode(), 0, 1);
    }
}
