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

use League\Uri;
use League\Uri\Exception as LeagueUriException;

class CheckUrl
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const PROTOCOL_HTTP = "http";
    const PROTOCOL_HTTPS = "https";

    private $method;
    private $protocol;
    private $domain;

    public function __construct(string $method, string $protocol, string $domain)
    {
        $this->assertIsValidMethod($method);
        $this->assertIsValidProtocol($protocol);

        $uri = $this->createUri($this->formatProtocol($protocol), $this->formatDomain($domain));
        $this->method = $this->formatMethod($method);
        $this->protocol = $uri->getScheme();
        $this->domain = $uri->getPath();
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

    private function createUri(string $scheme, string $path): Uri\Uri
    {
        try {
            $uri = Uri\Uri::createFromComponents([
                "scheme" => $scheme,
                "path" => $path
            ]);
        } catch (LeagueUriException $e) {
            throw new InvalidCheckDomainException(sprintf(
                'Problem generating the uri: %s',
                $e->getMessage()
            ));
        }
        if (!Uri\is_absolute($uri)) {
            throw new InvalidCheckDomainException(sprintf(
                'Uri is not a network path: %s',
                $uri
            ));
        }

        return $uri;
    }

    private function formatMethod($name): string
    {
        return strtoupper(trim($name));
    }

    private function formatProtocol($name): string
    {
        return strtolower(trim($name));
    }

    private function formatDomain($domain): string
    {
        return trim($domain);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function protocol(): string
    {
        return $this->protocol;
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function url(): string
    {
        return sprintf("%s://%s", $this->protocol(), $this->domain());
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
