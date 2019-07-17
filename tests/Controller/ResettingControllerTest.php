<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResettingControllerTest extends WebTestCase
{
    // TEST FONCTIONNEL
    public function testResettingPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://admin.fenrir-studio.fr/resetting/request');
        $this->assertEquals(
            0,
            $crawler->filter('html:contains("Username or email address")')->count()
        );
    }

    // TEST FONCTIONNEL
    public function testResettingRequestPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://admin.fenrir-studio.fr/resetting/request');
        $this->assertEquals(
            0,
            $crawler->filter('html:contains("Username or email address")')->count()
        );
    }
    // TEST FONCTIONNEL
    public function testResettingRequestResultPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://admin.fenrir-studio.fr/resetting/request');
        $form = $crawler->filter('form')->eq(0)->form();
        $form['username'] = 'Admin';
        $client->submit($form);
        $this->assertEquals(
            302,
            $client->getResponse()->getStatusCode()
        );
    }
}