<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    /** @test */
    public function guests_are_redirected_to_login_page(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
        $this->assertSame('http://localhost/login', $client->getResponse()->headers->get('Location'));
    }

    /** @test */
    public function users_view_home_page(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@mail.com',
            'PHP_AUTH_PW' => 'secret',
        ]);

        $crawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Home', $crawler->filter('title')->text());
    }
}
