<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Résout un champ de contenu éditorial bilingue (config/*.yaml) selon la
 * langue courante. Accepte aussi une simple chaîne (renvoyée telle quelle),
 * pour les champs pas encore traduits ou qui n'ont pas besoin de l'être
 * (noms propres, valeurs techniques).
 */
final class LocalizedContentExtension extends AbstractExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('localized', $this->localize(...)),
        ];
    }

    /**
     * @param array<string, string>|string|null $value
     */
    public function localize(array|string|null $value): ?string
    {
        if (!\is_array($value)) {
            return $value;
        }

        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';

        return $value[$locale] ?? $value['fr'] ?? (reset($value) ?: null);
    }
}
