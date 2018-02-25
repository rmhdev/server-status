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

namespace Infrastructure\Service;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ServerStatus\Infrastructure\Service\HttpPingService;
use ServerStatus\Tests\Domain\Model\Check\CheckUrlDataBuilder;

class HttpPingServiceTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnACorrectMeasureWhenTheCheckedSiteRespondsOk()
    {
        $client = new MockClient();
        $client->addResponse($this->createMockResponse(200));

        $service = new HttpPingService($client, MessageFactoryDiscovery::find());
        $result = $service->measure(
            CheckUrlDataBuilder::aCheckUrl()->build()
        );

        $this->assertEquals(200, $result->statusCode());
    }

    /**
     * @return ResponseInterface
     */
    private function createMockResponse(int $statusCode)
    {
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $response
            ->method('getStatusCode')->willReturn($statusCode)
        ;

        /**
         * @var $response ResponseInterface
         */
        return $response;
    }

    /**
     * @test
     */
    public function itShouldReturnAMeasureWhenTheCheckedSiteResponseIsNotSuccessful()
    {
        $client = new MockClient();
        $client->addResponse($this->createMockResponse(500));

        $service = new HttpPingService($client, MessageFactoryDiscovery::find());
        $result = $service->measure(
            CheckUrlDataBuilder::aCheckUrl()->build()
        );

        $this->assertEquals(500, $result->statusCode());
    }

    /**
     * @test
     */
    public function itShouldReturnAMeasureWhenThePingThrowsAnException()
    {
        $client = new MockClient();
        $client->addResponse($this->createMockResponseWithException());

        $service = new HttpPingService($client, MessageFactoryDiscovery::find());
        $result = $service->measure(
            CheckUrlDataBuilder::aCheckUrl()->build()
        );

        $this->assertEquals(0, $result->statusCode());
        $this->assertEquals("This is an exception", $result->reasonPhrase());
    }


    private function createMockResponseWithException()
    {
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $response
            ->method('getStatusCode')->willThrowException(new \Exception("This is an exception"));
        ;

        /**
         * @var $response ResponseInterface
         */
        return $response;
    }
}
