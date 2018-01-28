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

namespace ServerStatus\Model\Check;

class CheckUrl
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";

    private $method;

    public function __construct(string $method)
    {
        $this->assertIsValidMethod($method);
        $this->method = $this->formatMethod($method);
    }

    private function formatMethod($name): string
    {
        return strtoupper(trim($name));
    }

    /**
     * @param string $method
     * @throws InvalidCheckMethodException
     */
    private function assertIsValidMethod(string $method): void
    {
        $formatted = $this->formatMethod($method);
        if (in_array($formatted, self::methods())) {
            return;
        }

        throw new InvalidCheckMethodException(sprintf(
            'Method "%s" is not valid',
            $method
        ));
    }

    public function method(): string
    {
        return $this->method;
    }

    /**
     * @return string[]
     */
    public static function methods(): array
    {
        return [
            self::METHOD_GET,
            self::METHOD_POST
        ];
    }
}
