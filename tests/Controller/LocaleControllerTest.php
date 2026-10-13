<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LocaleControllerTest extends WebTestCase
{
    public function testRedirectsToRefererOnSameHost(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lang/en', server: ['HTTP_REFERER' => 'http://localhost/projects']);

        self::assertResponseRedirects('http://localhost/projects');
    }

    public function testFallsBackToHomeWhenRefererPointsToAnotherHost(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lang/en', server: ['HTTP_REFERER' => 'https://evil.example/phishing']);

        self::assertResponseRedirects('/');
    }

    public function testFallsBackToHomeWhenThereIsNoReferer(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lang/en');

        self::assertResponseRedirects('/');
    }

    public function testSetsLocaleCookieForOneYear(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lang/en');

        $cookie = $client->getResponse()->headers->getCookies()[0];

        self::assertSame('locale', $cookie->getName());
        self::assertSame('en', $cookie->getValue());
        self::assertGreaterThan(time() + 360 * 86400, $cookie->getExpiresTime());
    }

    public function testRejectsUnsupportedLocale(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lang/de');

        self::assertResponseStatusCodeSame(404);
    }
}
