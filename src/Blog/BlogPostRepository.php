<?php

declare(strict_types=1);

namespace App\Blog;

use App\Entity\BlogPost as BlogPostEntity;
use App\Repository\BlogPostRepository as BlogPostEntityRepository;
use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Résout les articles de blog (App\Entity\BlogPost, en base) pour une langue
 * donnée : choix du titre/résumé/contenu fr ou en, conversion du Markdown en
 * HTML. API publique (all/find) inchangée depuis la version fichiers
 * Markdown, pour ne rien casser côté BlogController / SitemapController.
 *
 * GithubFlavoredMarkdownConverter (pas juste CommonMarkConverter) : syntaxe
 * GFM (tableaux, texte barré, checkboxes/task-lists) que le CommonMark de
 * base ne sait pas interpréter.
 */
final class BlogPostRepository
{
    private readonly GithubFlavoredMarkdownConverter $converter;

    public function __construct(
        private readonly BlogPostEntityRepository $posts,
    ) {
        $this->converter = new GithubFlavoredMarkdownConverter();
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

    /**
     * @return list<BlogPost>
     */
    public function findByProject(string $projectSlug, string $locale): array
    {
        return array_map(
            fn (BlogPostEntity $post): BlogPost => $this->toDto($post, $locale),
            $this->posts->findByProjectSlug($projectSlug),
        );
    }

    private function toDto(BlogPostEntity $post, string $locale): BlogPost
    {
        $project = $post->getProject();

        return new BlogPost(
            slug: $post->getSlug(),
            title: $this->resolve($locale, $post->getTitleFr(), $post->getTitleEn()),
            summary: $this->resolve($locale, $post->getSummaryFr(), $post->getSummaryEn()),
            date: $post->getDate(),
            cover: $post->getCover(),
            contentHtml: (string) $this->converter->convert($this->resolve($locale, $post->getContentFr(), $post->getContentEn())),
            projectSlug: $project?->getSlug(),
            projectName: $project?->getName(),
        );
    }

    /**
     * Même logique de repli que le filtre Twig |localized : la langue
     * demandée si elle est renseignée, sinon le FR, sinon l'EN — une
     * traduction manquante (colonne vide, pas de valeur en base comme pour
     * les champs { fr, en }) ne doit jamais rendre une page vide.
     */
    private function resolve(string $locale, string $fr, string $en): string
    {
        $value = 'en' === $locale ? $en : $fr;

        if ('' !== $value) {
            return $value;
        }

        return '' !== $fr ? $fr : $en;
    }
}
