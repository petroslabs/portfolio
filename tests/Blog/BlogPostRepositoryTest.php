<?php

declare(strict_types=1);

namespace App\Tests\Blog;

use App\Blog\BlogPostRepository;
use App\Entity\BlogPost as BlogPostEntity;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BlogPostRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private BlogPostRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->repository = self::getContainer()->get(BlogPostRepository::class);
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    public function testFindResolvesRequestedLocale(): void
    {
        $this->persist($this->entity(titleFr: 'Bonjour', titleEn: 'Hello'));

        self::assertSame('Hello', $this->repository->find('hello-world', 'en')?->title);
        self::assertSame('Bonjour', $this->repository->find('hello-world', 'fr')?->title);
    }

    public function testFindFallsBackToFrenchWhenEnglishTranslationIsMissing(): void
    {
        $this->persist($this->entity(
            titleFr: 'Bonjour',
            titleEn: '',
            summaryFr: 'Résumé',
            summaryEn: '',
            contentFr: 'Contenu',
            contentEn: '',
        ));

        $resolved = $this->repository->find('hello-world', 'en');

        self::assertSame('Bonjour', $resolved?->title);
        self::assertSame('Résumé', $resolved?->summary);
        self::assertStringContainsString('Contenu', $resolved?->contentHtml ?? '');
    }

    public function testFindReturnsNullWhenPostDoesNotExist(): void
    {
        self::assertNull($this->repository->find('missing', 'fr'));
    }

    public function testFindConvertsMarkdownContentToHtml(): void
    {
        $this->persist($this->entity(contentFr: 'Du **gras** et du `code`.'));

        $html = $this->repository->find('hello-world', 'fr')?->contentHtml ?? '';

        self::assertStringContainsString('<strong>gras</strong>', $html);
        self::assertStringContainsString('<code>code</code>', $html);
    }

    public function testAllOrdersPostsByDateDescending(): void
    {
        $this->persist($this->entity(slug: 'older', date: new \DateTimeImmutable('2026-01-01')));
        $this->persist($this->entity(slug: 'newer', date: new \DateTimeImmutable('2026-06-01')));

        $slugs = array_map(static fn ($post) => $post->slug, $this->repository->all('fr'));

        // D'autres articles (seeds de migrations) peuvent déjà être en base :
        // on vérifie seulement l'ordre relatif des deux qu'on vient d'ajouter.
        self::assertLessThan(array_search('older', $slugs, true), array_search('newer', $slugs, true));
    }

    public function testPostsWithTheSameDateAreOrderedByMostRecentlyCreatedFirst(): void
    {
        $sameDate = new \DateTimeImmutable('2026-10-14');
        $this->persist($this->entity(slug: 'created-first', date: $sameDate));
        $this->persist($this->entity(slug: 'created-second', date: $sameDate));

        $slugs = array_map(static fn ($post) => $post->slug, $this->repository->all('fr'));

        self::assertLessThan(array_search('created-first', $slugs, true), array_search('created-second', $slugs, true));
    }

    public function testFindResolvesTheLinkedProject(): void
    {
        $project = (new Project())->setSlug('fixture-project-a')->setName('Fixture Project A')->setImage('images/projects/placeholder.webp');
        $this->entityManager->persist($project);
        $this->persist($this->entity()->setProject($project));

        $resolved = $this->repository->find('hello-world', 'fr');

        self::assertSame('fixture-project-a', $resolved?->projectSlug);
        self::assertSame('Fixture Project A', $resolved?->projectName);
    }

    public function testFindReturnsNullProjectWhenNoneIsLinked(): void
    {
        $this->persist($this->entity());

        $resolved = $this->repository->find('hello-world', 'fr');

        self::assertNull($resolved?->projectSlug);
        self::assertNull($resolved?->projectName);
    }

    public function testFindByProjectReturnsOnlyPostsLinkedToThatProject(): void
    {
        $project = (new Project())->setSlug('fixture-project-b')->setName('Fixture Project B')->setImage('images/projects/placeholder.webp');
        $this->entityManager->persist($project);
        $this->persist($this->entity(slug: 'linked')->setProject($project));
        $this->persist($this->entity(slug: 'unlinked'));

        $slugs = array_map(static fn ($post) => $post->slug, $this->repository->findByProject('fixture-project-b', 'fr'));

        self::assertSame(['linked'], $slugs);
    }

    private function entity(
        string $slug = 'hello-world',
        string $titleFr = 'Bonjour',
        string $titleEn = 'Hello',
        string $summaryFr = 'Résumé',
        string $summaryEn = 'Summary',
        string $contentFr = 'Contenu',
        string $contentEn = 'Content',
        ?\DateTimeImmutable $date = null,
    ): BlogPostEntity {
        return (new BlogPostEntity())
            ->setSlug($slug)
            ->setTitleFr($titleFr)
            ->setTitleEn($titleEn)
            ->setSummaryFr($summaryFr)
            ->setSummaryEn($summaryEn)
            ->setContentFr($contentFr)
            ->setContentEn($contentEn)
            ->setDate($date ?? new \DateTimeImmutable('today'));
    }

    private function persist(BlogPostEntity $post): void
    {
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}
