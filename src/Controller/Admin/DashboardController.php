<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\HubLinkRepository;
use App\Repository\ProjectRepository;
use App\Repository\UseCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Accueil de l'Admin ("/admin") : une carte par section de contenu. Hub,
 * Projets et Établi sont gérables depuis l'Admin ; le Blog reste en
 * Markdown — sa carte apparaît désactivée, à activer au moment de sa
 * migration en base.
 */
#[IsGranted('ROLE_ADMIN')]
final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function index(ProjectRepository $projects, HubLinkRepository $hubLinks, UseCategoryRepository $useCategories): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'projectCount' => \count($projects->findAllOrdered()),
            'hubLinkCount' => \count($hubLinks->findAllOrdered()),
            'useCategoryCount' => \count($useCategories->findAllOrdered()),
        ]);
    }
}
