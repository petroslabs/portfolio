<?php

declare(strict_types=1);

namespace App\Blog;

use App\Entity\BlogPost as BlogPostEntity;
use App\Repository\BlogPostRepository as BlogPostEntityRepository;
use League\CommonMark\CommonMarkConverter;

/**
 * Résout les articles de blog (App\Entity\BlogPost, en base — voir
 * /admin/blog) pour une langue donnée : choix du titre/résumé/contenu
 * fr ou en, conversion du Markdown en HTML. API publique (all/find)
 * inchangée depuis la version fichiers Markdown, pour ne rien casser côté
 * BlogController / SitemapController.
 */
final class BlogPostRepository
{
    private readonly CommonMarkConverter $converter;

    public function __construct(
        private readonly BlogPostEntityRepository $posts,
    ) {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @return list<BlogPost>
     */
    public function all(string $locale): array
    {
        return array_map(
            fn (BlogPostEntity $post): BlogPost => $this->toDto($post, $locale),
            $this->posts->findAllOrdered(),
        );
    }

    public function find(string $slug, string $locale): ?BlogPost
    {
        $post = $this->posts->findOneBy(['slug' => $slug]);

        return $post ? $this->toDto($post, $locale) : null;
    }

    private function toDto(BlogPostEntity $post, string $locale): BlogPost
    {
        $isEn = 'en' === $locale;

        return new BlogPost(
            slug: $post->getSlug(),
            title: $isEn ? $post->getTitleEn() : $post->getTitleFr(),
            summary: $isEn ? $post->getSummaryEn() : $post->getSummaryFr(),
            date: $post->getDate(),
            cover: $post->getCover(),
            contentHtml: (string) $this->converter->convert($isEn ? $post->getContentEn() : $post->getContentFr()),
        );
    }
}
