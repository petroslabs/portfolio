<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Vitrine des projets sur "/projects".
 *
 * Le contenu est injecté depuis config/projects.yaml via un bind défini dans
 * config/services.yaml — comme HomeController. Migrera en base de données
 * lorsque l'espace Admin sera implémenté (les projets y seront ajoutables
 * directement) ; d'ici là, aucune dépendance à Doctrine ici.
 */
final class ProjectController extends AbstractController
{
    /**
     * @param array<int, array{name: string, summary: string, image: string, stack: list<string>, status: string, repo_url: string, demo_url: ?string}> $projects
     */
    public function __construct(
        private readonly array $projects,
    ) {
    }

    #[Route('/projects', name: 'app_projects', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('projects/index.html.twig', [
            'projects' => $this->projects,
        ]);
    }
}
