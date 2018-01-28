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
    const PROTOCOL_HTTP = "http";
    const PROTOCOL_HTTPS = "https";

    private $method;
    private $protocol;

    public function __construct(string $method, string $protocol)
    {
        $this->assertIsValidMethod($method);
        $this->assertIsValidProtocol($protocol);
        $this->method = $this->formatMethod($method);
        $this->protocol = $this->formatProtocol($protocol);
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

    /**
     * @param string $protocol
     * @throws InvalidCheckProtocolException
     */
    private function assertIsValidProtocol(string $protocol): void
    {
        $formatted = $this->formatProtocol($protocol);
        if (in_array($formatted, self::protocols())) {
            return;
        }

        throw new InvalidCheckProtocolException(sprintf(
            'Protocol "%s" is not valid',
            $protocol
        ));
    }

    private function formatMethod($name): string
    {
        return strtoupper(trim($name));
    }

    private function formatProtocol($name): string
    {
        return strtolower(trim($name));
    }

    public function method(): string
    {
        return $this->method;
    }

    public function protocol(): string
    {
        return $this->protocol;
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

    /**
     * @return string[]
     */
    public static function protocols(): array
    {
        return [
            self::PROTOCOL_HTTP,
            self::PROTOCOL_HTTPS
        ];
    }
}
