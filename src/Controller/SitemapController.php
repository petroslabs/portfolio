<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\BlogPostRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Sitemap XML généré dynamiquement à partir des routes statiques du site,
 * des articles de blog et des fiches projet.
 */
final class SitemapController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $posts,
        private readonly ProjectRepository $projects,
    ) {
    }

    #[Route('/sitemap.xml', name: 'app_sitemap', methods: ['GET'])]
    public function index(): Response
    {
        $urls = [
            $this->generateUrl('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('app_projects', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('app_uses', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('app_blog', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        foreach ($this->posts->all('fr') as $post) {
            $urls[] = $this->generateUrl('app_blog_show', ['slug' => $post->slug], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        foreach ($this->projects->findAllOrdered() as $project) {
            $urls[] = $this->generateUrl('app_project_show', ['slug' => $project->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $response = $this->render('sitemap/index.xml.twig', ['urls' => $urls]);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
