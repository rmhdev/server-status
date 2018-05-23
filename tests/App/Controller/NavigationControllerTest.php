<?php

/**
 * This file is part of the server-status package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller;

class NavigationControllerTest extends AbstractControllerTest
{
    /**
     * @test
     */
    public function itShouldShowElementsInBarWhenAuthenticated()
    {
        $client = $this->authenticatedClient();
        $crawler = $client->request("GET", "/");

        $this->assertEquals(
            '/',
            $crawler->filter('body > nav .my-main-links .my-section-dashboard a')->attr('href')
        );
        $this->assertEquals(
            '/check/',
            $crawler->filter('body > nav .my-main-links .my-section-checks a')->attr('href')
        );
    }

    /**
     * @test
     */
    public function itShouldHideElementsInBarWhenNotAuthenticated()
    {
        $client = static::createClient();
        $crawler = $client->request("GET", "/");

        $this->assertEquals(0, $crawler->filter('body > nav .my-main-links')->count());
    }

    /**
     * @test
     */
    public function itShouldActivateCurrentSectionLink()
    {
        $client = $this->authenticatedClient();

        foreach ($this->sectionUris() as $uri) {
            $crawler = $client->request("GET", $uri);
            $this->assertEquals(
                $uri,
                $crawler->filter('body > nav .my-main-links .active a')->attr('href'),
                sprintf('When accessing "%s", its link in navbar should be active', $uri)
            );
        }
    }

    public function sectionUris()
    {
        return ['/', '/check/'];
    }

    /**
     * @test
     */
    public function itShouldActivateCurrentSectionLinkWhenNotAuthenticated()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(
            '/login',
            $crawler->filter('body > nav .active a')->attr('href')
        );
    }
}
