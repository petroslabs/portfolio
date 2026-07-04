<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Lit la langue préférée depuis le cookie posé par LocaleController et
 * l'applique à la requête — sans préfixe d'URL, les routes restent
 * identiques dans les deux langues.
 */
#[AsEventListener(event: KernelEvents::REQUEST, priority: 20)]
final class LocaleSubscriber
{
    private const array SUPPORTED_LOCALES = ['fr', 'en'];

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $locale = $request->cookies->get('locale');

        if (\in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $request->setLocale($locale);
        }
    }
}
