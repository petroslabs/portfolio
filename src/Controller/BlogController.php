<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Blog sur "/blog" (liste) et "/blog/{slug}" (article).
 *
 * Contenu en fichiers Markdown (content/blog/), pas de base de données —
 * voir App\Blog\BlogPostRepository.
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
