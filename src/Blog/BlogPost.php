<?php

declare(strict_types=1);

namespace App\Blog;

/**
 * Article de blog résolu pour une langue donnée (métadonnées + corps HTML).
 */
final readonly class BlogPost
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $summary,
        public \DateTimeImmutable $date,
        public ?string $cover,
        public string $contentHtml,
    ) {
    }
}
