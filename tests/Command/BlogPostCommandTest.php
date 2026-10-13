<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\BlogPostCommand;
use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class BlogPostCommandTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CommandTester $tester;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager->getConnection()->beginTransaction();

        $this->tester = new CommandTester(self::getContainer()->get(BlogPostCommand::class));
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    public function testCreatesNewPostFromMarkdownFiles(): void
    {
        $this->tester->setInputs([
            'test-post',
            'Titre FR',
            'Title EN',
            'Résumé FR',
            'Summary EN',
            $this->tempMarkdownFile('Contenu **FR**.'),
            $this->tempMarkdownFile('Content **EN**.'),
            '',
            '2026-05-01',
        ]);

        self::assertSame(0, $this->tester->execute([]));

        $post = $this->entityManager->getRepository(BlogPost::class)->findOneBy(['slug' => 'test-post']);
        self::assertNotNull($post);
        self::assertSame('Titre FR', $post->getTitleFr());
        self::assertSame('Contenu **FR**.', $post->getContentFr());
        self::assertSame('2026-05-01', $post->getDate()->format('Y-m-d'));
    }

    public function testUpdatingExistingPostKeepsValuesOnBlankAnswer(): void
    {
        $existing = (new BlogPost())
            ->setSlug('existing-post')
            ->setTitleFr('Ancien titre')
            ->setTitleEn('Old title')
            ->setSummaryFr('Ancien résumé')
            ->setSummaryEn('Old summary')
            ->setContentFr('Ancien contenu')
            ->setContentEn('Old content');
        $this->entityManager->persist($existing);
        $this->entityManager->flush();

        $this->tester->setInputs([
            'existing-post',
            '', // titleFr -> gardé
            'New title', // titleEn -> changé
            '', // summaryFr -> gardé
            '', // summaryEn -> gardé
            '', // contentFr -> gardé
            '', // contentEn -> gardé
            '', // cover -> gardé (aucun)
            $existing->getDate()->format('Y-m-d'),
        ]);

        self::assertSame(0, $this->tester->execute([]));

        $this->entityManager->refresh($existing);
        self::assertSame('Ancien titre', $existing->getTitleFr());
        self::assertSame('New title', $existing->getTitleEn());
        self::assertSame('Ancien contenu', $existing->getContentFr());
    }

    public function testRejectsInvalidSlugThenAcceptsRetry(): void
    {
        $this->tester->setInputs([
            'Invalid Slug!',
            'valid-slug',
            'Titre',
            'Title',
            'Résumé',
            'Summary',
            $this->tempMarkdownFile('FR'),
            $this->tempMarkdownFile('EN'),
            '',
            '2026-01-01',
        ]);

        self::assertSame(0, $this->tester->execute([]));
        self::assertStringContainsString('Slug invalide', $this->tester->getDisplay());
    }

    private function tempMarkdownFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'blog-test-').'.md';
        file_put_contents($path, $content);

        return $path;
    }
}
