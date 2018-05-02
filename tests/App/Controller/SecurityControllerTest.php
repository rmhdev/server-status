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

class SecurityControllerTest extends WebTestCase
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

    private function authenticatedClient()
    {
        $client = static::createClient();
        $crawler = $client->request("GET", "/login");
        $form = $crawler->selectButton("Submit")->form([
            "_username" => "rober@example.com",
            "_password" => "123456"
        ], "POST");
        $client->submit($form);
        $client->followRedirect();

        return $client;
    }
}
