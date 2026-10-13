<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\BlogPost;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SitemapControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
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

    public function testSitemapListsStaticRoutesAndDynamicContent(): void
    {
        $this->entityManager->persist(
            (new BlogPost())
                ->setSlug('my-test-post')
                ->setTitleFr('Titre')
                ->setTitleEn('Title')
                ->setSummaryFr('Résumé')
                ->setSummaryEn('Summary')
                ->setContentFr('Contenu')
                ->setContentEn('Content'),
        );
        $this->entityManager->persist(
            (new Project())
                ->setSlug('my-test-project')
                ->setName('Test project')
                ->setImage('images/projects/placeholder.webp'),
        );
        $this->entityManager->flush();

        $this->client->request('GET', '/sitemap.xml');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('text/xml', $this->client->getResponse()->headers->get('Content-Type'));

        $xml = $this->client->getResponse()->getContent();
        self::assertNotFalse($xml);

        foreach (['/', '/projects', '/uses', '/blog', '/blog/my-test-post', '/projects/my-test-project'] as $path) {
            self::assertStringContainsString("<loc>http://localhost{$path}</loc>", $xml);
        }
    }
}
