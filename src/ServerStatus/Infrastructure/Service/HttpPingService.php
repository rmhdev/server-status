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
use Http\Client\Exception as HttpClientException;
use Http\Message\MessageFactory;
use ServerStatus\Domain\Model\Check\Check;
use ServerStatus\Domain\Model\Measurement\Measurement;
use ServerStatus\Domain\Model\Measurement\MeasurementId;
use ServerStatus\Domain\Model\Measurement\MeasurementResult;
use Symfony\Component\Stopwatch\Stopwatch;

final class HttpPingService implements PingService
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
            $check,
            $this->createMeasurementResult($check)
        );

        return $measurement;
    }

    private function createMeasurementResult(Check $check): MeasurementResult
    {
        $stopWatch = new Stopwatch(true);
        $stopWatch->start("measurement");
        try {
            $request = $this->messageFactory->createRequest(
                $check->url()->method(),
                $check->url()->url()
            );
            $response = $this->httpClient->sendRequest($request);
            $code = $response->getStatusCode();
            $reasonPhrase = (string) $response->getReasonPhrase();
        } catch (HttpClientException $httpException) {
            $code = 0;
            $reasonPhrase = $httpException->getMessage();
        } catch (\Exception $e) {
            $code = 0;
            $reasonPhrase = $e->getMessage();

        }
        $event = $stopWatch->stop("measurement");

        return new MeasurementResult($code, $reasonPhrase, $event->getDuration(), $event->getMemory());
    }
}