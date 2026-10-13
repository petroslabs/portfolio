<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProjectRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ProjectRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->repository = self::getContainer()->get(ProjectRepository::class);
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

    public function testPinnedProjectsComeFirstRegardlessOfPosition(): void
    {
        $this->persist($this->project(slug: 'unpinned-first', position: 0, pinned: false));
        $this->persist($this->project(slug: 'pinned-last', position: 99, pinned: true));

        $slugs = $this->orderedSlugs();

        self::assertLessThan(
            array_search('unpinned-first', $slugs, true),
            array_search('pinned-last', $slugs, true),
        );
    }

    public function testProjectsWithSamePinnedStatusAreOrderedByPosition(): void
    {
        $this->persist($this->project(slug: 'second', position: 2, pinned: true));
        $this->persist($this->project(slug: 'first', position: 1, pinned: true));

        $slugs = $this->orderedSlugs();

        self::assertLessThan(
            array_search('second', $slugs, true),
            array_search('first', $slugs, true),
        );
    }

    public function testFindOneBySlugReturnsNullWhenNotFound(): void
    {
        self::assertNull($this->repository->findOneBySlug('does-not-exist'));
    }

    /**
     * @return list<string>
     */
    private function orderedSlugs(): array
    {
        return array_map(static fn (Project $project): string => $project->getSlug(), $this->repository->findAllOrdered());
    }

    private function project(string $slug, int $position, bool $pinned): Project
    {
        return (new Project())
            ->setSlug($slug)
            ->setName($slug)
            ->setImage('images/projects/placeholder.webp')
            ->setPosition($position)
            ->setPinned($pinned);
    }

    private function persist(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
}
