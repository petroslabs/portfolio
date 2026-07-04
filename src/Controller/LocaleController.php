<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Bascule la langue du site (cookie 1 an) et redirige vers la page d'origine.
 */
final class LocaleController extends AbstractController
{
    #[Route('/lang/{locale}', name: 'app_switch_locale', requirements: ['locale' => 'fr|en'], methods: ['GET'])]
    public function switch(string $locale, Request $request): RedirectResponse
    {
        $response = new RedirectResponse($this->safeRedirectTarget($request));
        $response->headers->setCookie(
            Cookie::create('locale', $locale, strtotime('+1 year'), sameSite: Cookie::SAMESITE_LAX),
        );

        return $response;
    }

    /**
     * N'utilise le Referer que s'il pointe vers ce même site (anti open-redirect).
     */
    private function safeRedirectTarget(Request $request): string
    {
        $referer = $request->headers->get('referer');

        if ($referer && parse_url($referer, \PHP_URL_HOST) === $request->getHost()) {
            return $referer;
        }

        return $this->generateUrl('app_home');
    }
}
