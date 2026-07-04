<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\HubLinkRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Accueil de l'Admin ("/admin") : une carte par section de contenu. Hub et
 * Projets sont gérables depuis l'Admin ; Établi/Blog restent en
 * YAML/Markdown — leurs cartes apparaissent désactivées, à activer au fur
 * et à mesure de leur migration en base.
 */
#[IsGranted('ROLE_ADMIN')]
final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function index(ProjectRepository $projects, HubLinkRepository $hubLinks): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'projectCount' => \count($projects->findAllOrdered()),
            'hubLinkCount' => \count($hubLinks->findAllOrdered()),
        ]);
    }
}
