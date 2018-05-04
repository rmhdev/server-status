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

class SecurityControllerTest extends AbstractControllerTest
{
    /**
     * @test
     */
    public function loginCorrectUserShouldAuthenticate()
    {
        $client = $this->authenticatedClient();
        $expectedUri = $client->request("GET", "/")->getUri();

        $this->assertEquals($expectedUri, $client->getHistory()->current()->getUri());
    }

    /**
     * @test
     */
    public function notAuthenticatedUserShouldHaveLoginLink()
    {
        $client = static::createClient();
        $crawler = $client->request("GET", "/");

        $this->assertEquals(
            '/login',
            $crawler->filter('a.sign-in')->attr("href"),
            'User should have a link to the login form'
        );
    }

    /**
     * @test
     */
    public function authenticatedUserShouldHaveLogoutLink()
    {
        $client = $this->authenticatedClient();
        $crawler = $client->getCrawler();

        $this->assertEquals(
            '/logout',
            $crawler->filter('a.sign-out')->attr('href'),
            'Authenticated user should have a link to logout'
        );
        $this->assertEquals(
            0,
            $crawler->filter('a.sign-in')->count(),
            'The login link should not appear'
        );
    }

    /**
     * @test
     */
    public function accessToAccountByNotAuthenticatedUserShouldRedirectToLogin()
    {
        $client = static::createClient();
        $client->request("GET", "/account");

        $this->assertTrue($client->getResponse()->isRedirection());
        $client->followRedirect();

        $crawler = $client->request("GET", "/");
        $this->assertEquals($crawler->getUri(), $client->getHistory()->current()->getUri());
    }

    /**
     * @test
     */
    public function logoutAuthenticatedUserShouldRedirectToHomepage()
    {
        $client = $this->authenticatedClient();
        $expectedUrl = $client->request("GET", "/")->getUri();
        $link = $client->getCrawler()
            ->filter('a.sign-out')
            ->first()
            ->link()
        ;
        $client->click($link);
        $client->followRedirect();

        $this->assertEquals(
            $expectedUrl,
            $client->getHistory()->current()->getUri(),
            'Logout redirects to homepage'
        );
    }
}
