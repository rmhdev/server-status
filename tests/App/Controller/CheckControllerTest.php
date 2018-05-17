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
     * @test
     */
    public function itShouldShowBasicInfoByCustomer()
    {
        $client = $this->authenticatedClient();
        $crawler = $client->request("GET", "/check/");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Checks', $crawler->filter('title')->text());
    }

    /**
     * @test
     */
    public function isShouldListSortedChecksByCustomer()
    {
        $client = $this->authenticatedClient();
        $crawler = $client->request("GET", "/check/");

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
}
