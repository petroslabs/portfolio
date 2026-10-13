<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Blog sur "/blog" (liste), "/blog/{slug}" (article) et "/blog/rss.xml"
 * (flux RSS). Contenu en base de données — voir App\Blog\BlogPostRepository.
 */
final class BlogController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $posts,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[Route('/blog', name: 'app_blog', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $this->posts->all($this->locale()),
        ]);
    }

    #[Route('/blog/rss.xml', name: 'app_blog_rss', methods: ['GET'])]
    public function rss(): Response
    {
        $response = $this->render('blog/rss.xml.twig', [
            // Même limite assumée que le reste du SEO : pas de hreflang
            // correct possible avec l'i18n cookie/sans-préfixe, donc un seul
            // flux FR plutôt qu'une version par langue.
            'posts' => $this->posts->all('fr'),
        ]);
        $response->headers->set('Content-Type', 'application/rss+xml; charset=UTF-8');

        return $response;
    }

    #[Route('/blog/{slug}', name: 'app_blog_show', methods: ['GET'])]
    public function show(string $slug): Response
    {
        $post = $this->posts->find($slug, $this->locale());

        if (null === $post) {
            throw $this->createNotFoundException();
        }

        return $this->render('blog/show.html.twig', [
            'post' => $post,
        ]);
    }

    private function locale(): string
    {
        return $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
    }
}
