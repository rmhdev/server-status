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

namespace ServerStatus\Domain\Model\Check;

class CheckStatus
{
    const CODE_ENABLED = "enabled";
    const CODE_DISABLED = "disabled";

    /**
     * @var string
     */
    private $code;

    public function __construct(string $code = "")
    {
        $processed = $this->processCode($code);
        $this->assertCodeIsCorrect($processed);
        $this->code = $processed;
    }

    private function processCode(string $code = "")
    {
        if ("" === $code) {
            $code = self::CODE_ENABLED;
        }

        return $code;
    }

    private function assertCodeIsCorrect($code)
    {
        if (!in_array($code, self::codes())) {
            throw new InvalidCheckStatusException(sprintf(
                'Code "%s" is not correct; codes accepted: %s',
                $code,
                implode(", ", self::codes())
            ));
        }
    }

    public function name(): string
    {
        return $this->code;
    }

    public function isEnabled(): bool
    {
        return $this->isCode(self::CODE_ENABLED);
    }

    private function isCode($code): bool
    {
        return $code === $this->code;
    }

    /**
     * @return string[]
     */
    public static function codes(): array
    {
        return [
            self::CODE_ENABLED,
            self::CODE_DISABLED,
        ];
    }
}
