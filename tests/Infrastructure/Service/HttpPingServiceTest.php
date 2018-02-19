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
use ServerStatus\Tests\Domain\Model\Check\CheckDataBuilder;

class HttpPingServiceTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldReturnACorrectMeasureWhenTheCheckedSiteRespondsOk()
    {
        $client = new MockClient();
        $client->addResponse($this->createMockResponse(200));

        $check = CheckDataBuilder::aCheck()->build();
        $service = new HttpPingService($client, MessageFactoryDiscovery::find());
        $measurement = $service->measure($check);

        $this->assertEquals(200, $measurement->result()->code());
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
}
