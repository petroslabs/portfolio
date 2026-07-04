<?php

declare(strict_types=1);

namespace App\Blog;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Yaml\Yaml;

/**
 * Articles de blog stockés en fichiers Markdown dans content/blog/
 * (un fichier par article et par langue : {slug}.fr.md / {slug}.en.md),
 * avec un frontmatter YAML pour les métadonnées (title, summary, date, cover).
 *
 * Même logique que hub.yaml/projects.yaml/uses.yaml : pas de base de données
 * tant que l'espace Admin n'existe pas. Si la traduction d'une langue
 * manque pour un article, on retombe sur le FR (comme le filtre |localized).
 */
final class BlogPostRepository
{
    private const array LOCALES = ['fr', 'en'];

    /** @var array<string, array<string, array{title: string, summary: string, date: \DateTimeImmutable, cover: ?string, contentHtml: string}>>|null */
    private ?array $bySlug = null;

    private readonly CommonMarkConverter $converter;

    public function __construct(
        private readonly string $contentDir,
    ) {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @return list<BlogPost>
     */
    public function all(string $locale): array
    {
        $posts = array_map(
            fn (string $slug): BlogPost => $this->build($slug, $locale),
            array_keys($this->load()),
        );

        usort($posts, static fn (BlogPost $a, BlogPost $b): int => $b->date <=> $a->date);

        return $posts;
    }

    public function find(string $slug, string $locale): ?BlogPost
    {
        if (!isset($this->load()[$slug])) {
            return null;
        }

        return $this->build($slug, $locale);
    }

    private function build(string $slug, string $locale): BlogPost
    {
        $translations = $this->load()[$slug];
        $data = $translations[$locale] ?? $translations['fr'];

        return new BlogPost(
            slug: $slug,
            title: $data['title'],
            summary: $data['summary'],
            date: $data['date'],
            cover: $data['cover'],
            contentHtml: $data['contentHtml'],
        );
    }

    /**
     * @return array<string, array<string, array{title: string, summary: string, date: \DateTimeImmutable, cover: ?string, contentHtml: string}>>
     */
    private function load(): array
    {
        if (null !== $this->bySlug) {
            return $this->bySlug;
        }

        $this->bySlug = [];

        foreach (glob($this->contentDir.'/*.md') ?: [] as $path) {
            $filename = basename($path, '.md');
            $parts = explode('.', $filename);
            $locale = array_pop($parts);
            $slug = implode('.', $parts);

            if ('' === $slug || !\in_array($locale, self::LOCALES, true)) {
                continue;
            }

            $this->bySlug[$slug][$locale] = $this->parse($path);
        }

        return $this->bySlug;
    }

    /**
     * @return array{title: string, summary: string, date: \DateTimeImmutable, cover: ?string, contentHtml: string}
     */
    private function parse(string $path): array
    {
        $raw = file_get_contents($path);

        if (false === $raw || !preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $raw, $matches)) {
            throw new \RuntimeException(\sprintf('Article de blog "%s" : frontmatter YAML manquant ou invalide.', $path));
        }

        $frontmatter = Yaml::parse($matches[1]);
        $rawDate = $frontmatter['date'];

        return [
            'title' => (string) $frontmatter['title'],
            'summary' => (string) $frontmatter['summary'],
            // Un timestamp non quoté en YAML (ex. `date: 2026-07-04`) est parsé
            // par le composant Yaml comme un entier Unix, pas une chaîne.
            'date' => $rawDate instanceof \DateTimeInterface
                ? \DateTimeImmutable::createFromInterface($rawDate)
                : (\is_int($rawDate) ? (new \DateTimeImmutable())->setTimestamp($rawDate) : new \DateTimeImmutable((string) $rawDate)),
            'cover' => $frontmatter['cover'] ?? null,
            'contentHtml' => (string) $this->converter->convert(trim($matches[2])),
        ];
    }
}
