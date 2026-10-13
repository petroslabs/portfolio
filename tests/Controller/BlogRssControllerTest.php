<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BlogRssControllerTest extends WebTestCase
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

    public function testFeedListsPostsAsRssItems(): void
    {
        $this->entityManager->persist(
            (new BlogPost())
                ->setSlug('rss-test-post')
                ->setTitleFr('Titre RSS')
                ->setTitleEn('RSS title')
                ->setSummaryFr('Résumé RSS')
                ->setSummaryEn('RSS summary')
                ->setContentFr('Contenu')
                ->setContentEn('Content')
                ->setDate(new \DateTimeImmutable('2026-03-15')),
        );
        $this->entityManager->flush();

        $this->client->request('GET', '/blog/rss.xml');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('application/rss+xml', $this->client->getResponse()->headers->get('Content-Type'));

        $xml = $this->client->getResponse()->getContent();
        self::assertNotFalse($xml);

        $feed = new \SimpleXMLElement($xml);
        self::assertSame('2.0', (string) $feed['version']);

        // iterator_to_array() par défaut préserve les clés — or l'itérateur
        // de SimpleXMLElement renvoie le nom de balise ("item") comme clé
        // pour chaque élément, donc chaque entrée écraserait la précédente.
        $titles = array_map(static fn ($item): string => (string) $item->title, iterator_to_array($feed->channel->item, false));
        self::assertContains('Titre RSS', $titles);

        $item = $feed->channel->item[array_search('Titre RSS', $titles, true)];
        self::assertSame('http://localhost/blog/rss-test-post', (string) $item->link);
        self::assertSame('Résumé RSS', (string) $item->description);
        self::assertSame(
            (new \DateTimeImmutable('2026-03-15'))->format(\DateTimeInterface::RSS),
            (string) $item->pubDate,
        );
    }

    public function testFeedIsValidXmlEvenWithNoPosts(): void
    {
        $this->client->request('GET', '/blog/rss.xml');

        self::assertResponseIsSuccessful();
        $feed = new \SimpleXMLElement((string) $this->client->getResponse()->getContent());
        self::assertSame('rss', $feed->getName());
    }
}
