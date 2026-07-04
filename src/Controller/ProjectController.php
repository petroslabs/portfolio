<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Vitrine des projets sur "/projects".
 *
 * Contenu géré en base de données (App\Entity\Project), éditable depuis
 * l'espace Admin (/admin/projects).
 */
final class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $projects,
    ) {
    }

    #[Route('/projects', name: 'app_projects', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('projects/index.html.twig', [
            'projects' => $this->projects->findAllOrdered(),
        ]);
    }
}
