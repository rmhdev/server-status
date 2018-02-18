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

namespace ServerStatus\Infrastructure\Service;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementId;

final class HttpPingService
{
    private $httpClient;
    private $messageFactory;

    public function __construct(HttpClient $httpClient, MessageFactory $messageFactory)
    {
        $this->httpClient = $httpClient;
        $this->messageFactory = $messageFactory;
    }

    public function measure(Check $check): Measurement
    {
        return $this->createMeasurement($check);
    }

    private function createMeasurement(Check $check)
    {
        $measurement = new Measurement(
            new MeasurementId(),
            new \DateTimeImmutable("now"),
            $check
        );

        return $measurement;
    }
}
