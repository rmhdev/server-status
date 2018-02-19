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

class MeasurementResult
{
    const STATUS_CODE_CLASS_SUCCESSFUL = 2;

    private $code;

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function isSuccessful(): bool
    {
        return $this->isStatusCodeClass(self::STATUS_CODE_CLASS_SUCCESSFUL);
    }

    private function isStatusCodeClass(int $classNumber): bool
    {
        return $classNumber === (int) substr((string) $this->code(), 0, 1);
    }
}
