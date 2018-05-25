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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function itShouldLoadTheHomePage()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function itShouldShowTheProjectName()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertContains(
            'aktibo',
            strtolower($crawler->filter('body > nav a.navbar-brand')->text())
        );
        $this->assertContains(
            '/',
            $crawler->filter('body > nav a.navbar-brand')->attr("href")
        );
        $this->assertContains(
            'aktibo',
            strtolower($crawler->filter('title')->text()),
            'Title of the page should contain the project name'
        );
    }
}
