<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\EventListener\LocaleSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class LocaleSubscriberTest extends TestCase
{
    // Locale de départ simulant celle posée par le LocaleListener natif de
    // Symfony (framework.default_locale: fr, en prod) avant que notre
    // subscriber (priorité 20) ne s'exécute — un Request fraîchement créé
    // hors kernel ne la porte pas (défaut interne de Symfony : 'en').
    private const string DEFAULT_LOCALE = 'fr';

    public function testAppliesSupportedLocaleFromCookie(): void
    {
        $event = $this->mainRequestEventWithCookie('en');

        (new LocaleSubscriber())($event);

        self::assertSame('en', $event->getRequest()->getLocale());
    }

    public function testIgnoresUnsupportedLocale(): void
    {
        $event = $this->mainRequestEventWithCookie('de');

        (new LocaleSubscriber())($event);

        self::assertSame(self::DEFAULT_LOCALE, $event->getRequest()->getLocale());
    }

    public function testIgnoresMissingCookie(): void
    {
        $event = $this->mainRequestEvent(Request::create('/'));

        (new LocaleSubscriber())($event);

        self::assertSame(self::DEFAULT_LOCALE, $event->getRequest()->getLocale());
    }

    public function testIgnoresSubRequests(): void
    {
        $request = Request::create('/');
        $request->setLocale(self::DEFAULT_LOCALE);
        $request->cookies->set('locale', 'en');
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        (new LocaleSubscriber())($event);

        self::assertSame(self::DEFAULT_LOCALE, $event->getRequest()->getLocale());
    }

    private function mainRequestEventWithCookie(string $locale): RequestEvent
    {
        $request = Request::create('/');
        $request->cookies->set('locale', $locale);

        return $this->mainRequestEvent($request);
    }

    private function mainRequestEvent(Request $request): RequestEvent
    {
        $request->setLocale(self::DEFAULT_LOCALE);
        $kernel = $this->createStub(HttpKernelInterface::class);

        return new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);
    }
}
