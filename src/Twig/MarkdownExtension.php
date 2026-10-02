<?php

declare(strict_types=1);

namespace App\Twig;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Convertit du Markdown en HTML. GithubFlavoredMarkdownConverter (pas juste
 * CommonMarkConverter) pour rester cohérent avec le contenu généré par
 * l'éditeur WYSIWYG (Toast UI) utilisé dans l'admin — voir
 * App\Blog\BlogPostRepository, qui fait la même conversion côté blog.
 */
final class MarkdownExtension extends AbstractExtension
{
    private readonly GithubFlavoredMarkdownConverter $converter;

    public function __construct()
    {
        $this->converter = new GithubFlavoredMarkdownConverter();
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', $this->toHtml(...), ['is_safe' => ['html']]),
        ];
    }

    public function toHtml(?string $markdown): string
    {
        if (null === $markdown || '' === $markdown) {
            return '';
        }

        return (string) $this->converter->convert($markdown);
    }
}
