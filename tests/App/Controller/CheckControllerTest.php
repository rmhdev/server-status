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

class CheckControllerTest extends AbstractControllerTest
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
    public function itShouldShowBasicInfoByCustomer()
    {
        $crawler = $this->authenticatedClient->request("GET", "/check/");

        $this->assertEquals(200, $this->authenticatedClient->getResponse()->getStatusCode());
        $this->assertContains('Checks', $crawler->filter('title')->text());
    }

    /**
     * @test
     */
    public function isShouldListSortedChecksByCustomer()
    {
        $crawler = $this->authenticatedClient->request("GET", "/check/");

        $this->assertEquals(3, $crawler->filter("body > main .checks .check header a")->count());
        $this->assertEquals(
            '/check/my-first-check',
            $crawler->filter("body > main .checks .check header a")->first()->attr('href'),
            'Link to first check'
        );
        $this->assertEquals(
            '/check/my-disabled-check',
            $crawler->filter("body > main .checks .check header a")->last()->attr('href'),
            'Link to last check'
        );
    }

    /**
     * @test
     */
    public function itShouldListChecksUsingCustomDateAndType()
    {
        $crawler = $this->authenticatedClient->request("GET", "/check/?date=2018-01-01&type=day");

        $this->assertContains(
            'January 1, 2018',
            $crawler->filter('body > main header .subtitle')->text()
        );
        $this->assertContains(
            'day performance',
            $crawler->filter('body > main header .subtitle')->text()
        );
    }

    /**
     * @test
     */
    public function itShouldShowProfileOfSingleCheck()
    {
        $crawler = $this->authenticatedClient->request("GET", "/check/my-first-check");

        $this->assertEquals(200, $this->authenticatedClient->getResponse()->getStatusCode(), 'Check page exists');
        $this->assertContains(
            'My first check',
            $crawler->filter('body > main header .header-title')->text()
        );
    }

    /**
     * @test
     */
    public function itShouldShowCheckValuesUsingCustomDateAndType()
    {
        $crawler = $this->authenticatedClient->request("GET", "/check/my-first-check?date=2018-01-01&type=day");

        $this->assertContains(
            'January 1, 2018',
            $crawler->filter('body > main header .subtitle')->text(),
            $crawler->filter('body > main header .subtitle')->text()
        );
        $this->assertContains(
            'day performance',
            $crawler->filter('body > main header .subtitle')->text()
        );
    }
}
