<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\BlogPostRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Vitrine des projets sur "/projects".
 *
 * Contenu géré en base de données (App\Entity\Project).
 */
final class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly BlogPostRepository $posts,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[Route('/projects', name: 'app_projects', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('projects/index.html.twig', [
            'projects' => $this->projects->findAllOrdered(),
        ]);
    }

    #[Route('/projects/{slug}', name: 'app_project_show', methods: ['GET'])]
    public function show(string $slug): Response
    {
        $project = $this->projects->findOneBySlug($slug);

        if (null === $project) {
            throw $this->createNotFoundException();
        }

        return $this->render('projects/show.html.twig', [
            'project' => $project,
            'relatedPosts' => $this->posts->findByProject($slug, $this->locale()),
        ]);
    }

    private function locale(): string
    {
        return $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
    }
}
