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

namespace App\Tests\Controller;

class AlertControllerTest extends AbstractControllerTest
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client;
     */
    private $authenticatedClient;


    protected function setUp()
    {
        parent::setUp();
        $this->authenticatedClient = $this->authenticatedClient();
    }

    protected function tearDown()
    {
        unset($this->authenticatedClient);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function itShouldListOfAlertsOfCustomer()
    {
        $crawler = $this->authenticatedClient->request("GET", "/alert/");

        $this->assertEquals(200, $this->authenticatedClient->getResponse()->getStatusCode());
        $this->assertContains('Alerts', $crawler->filter('title')->text());
    }

    /**
     * @test
     */
    public function itShouldListAlertsOrderedByName()
    {
        $crawler = $this->authenticatedClient->request("GET", "/alert/");

        $this->assertEquals(3, $crawler->filter("body > main .app-alerts .app-alert")->count());
    }
}
