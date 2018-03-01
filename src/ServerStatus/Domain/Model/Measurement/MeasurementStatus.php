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
    const CLASS_RESPONSE_INFORMATIONAL = 1;
    const CLASS_RESPONSE_SUCCESSFUL = 2;
    const CLASS_RESPONSE_REDIRECTION = 3;
    const CLASS_RESPONSE_CLIENT_ERROR = 4;
    const CLASS_RESPONSE_SERVER_ERROR = 5;

    /**
     * @var int
     */
    private $code;

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
        $this->code = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @return int The response status code
     */
    public function code(): int
    {
        return $this->code;
    }

    public function reasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function isInformational(): bool
    {
        return $this->isStatusCodeClass(self::CLASS_RESPONSE_INFORMATIONAL);
    }

    public function isSuccessful(): bool
    {
        return $this->isStatusCodeClass(self::CLASS_RESPONSE_SUCCESSFUL);
    }

    public function isRedirection(): bool
    {
        return $this->isStatusCodeClass(self::CLASS_RESPONSE_REDIRECTION);
    }

    public function isClientError(): bool
    {
        return $this->isStatusCodeClass(self::CLASS_RESPONSE_CLIENT_ERROR);
    }

    public function isServerError(): bool
    {
        return $this->isStatusCodeClass(self::CLASS_RESPONSE_SERVER_ERROR);
    }

    public function isInternalError(): bool
    {
        return 0 == $this->classResponse();
    }

    public function statusName(): string
    {
        switch ($this->classResponse()) {
            case self::CLASS_RESPONSE_INFORMATIONAL:
                return "informational";
            case self::CLASS_RESPONSE_SUCCESSFUL:
                return "successful";
            case self::CLASS_RESPONSE_REDIRECTION:
                return "redirection";
            case self::CLASS_RESPONSE_CLIENT_ERROR:
                return "client_error";
            case self::CLASS_RESPONSE_SERVER_ERROR:
                return "server_error";
            default:
                return "error";
        }
    }

    private function isStatusCodeClass(int $classNumber): bool
    {
        return $classNumber === $this->classResponse();
    }

    public function equals(MeasurementStatus $status): bool
    {
        return $status->code() === $this->code();
    }

    /**
     * @return int &lt; 0 if $this is less than
     * $status; &gt; 0 if $this
     * is greater than $status, and 0 if they are
     * equal.
     */
    public function compareTo(MeasurementStatus $status): int
    {
        return strcmp((string) $this->code(), (string) $status->code());
    }

    /**
     * First digit of the status code.
     *
     * @return int
     */
    public function classResponse(): int
    {
        return (int) substr((string) $this->code(), 0, 1);
    }

    public function hasSameClassResponse(MeasurementStatus $status): bool
    {
        return $this->classResponse() === $status->classResponse();
    }

    /**
     * @return int[]
     */
    public static function correctClassResponses(): array
    {
        return [
            self::CLASS_RESPONSE_INFORMATIONAL,
            self::CLASS_RESPONSE_SUCCESSFUL,
            self::CLASS_RESPONSE_REDIRECTION
        ];
    }

    /**
     * @return int[]
     */
    public static function incorrectClassResponses(): array
    {
        return array_diff(self::classResponses(), self::correctClassResponses());
    }

    /**
     * @return int[]
     */
    public static function classResponses(): array
    {
        return [
            self::CLASS_RESPONSE_INFORMATIONAL,
            self::CLASS_RESPONSE_SUCCESSFUL,
            self::CLASS_RESPONSE_REDIRECTION,
            self::CLASS_RESPONSE_CLIENT_ERROR,
            self::CLASS_RESPONSE_SERVER_ERROR,
        ];
    }
}
