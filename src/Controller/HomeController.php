<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Landing page (hub central) sur "/".
 *
 * Le contenu (profil + liens) est injecté depuis config/hub.yaml via un bind
 * défini dans config/services.yaml. On passera en base de données lorsque
 * l'espace Admin sera implémenté — d'ici là, aucune dépendance à Doctrine ici.
 *
 * Ce contrôleur est volontairement isolé pour cohabiter proprement avec les
 * futurs BlogController, ProjectController et l'espace Admin\.
 */
final class HomeController extends AbstractController
{
    /**
     * @param array{name: string, tagline: string, bio: string, banner: string, logo: string} $profile
     * @param array<int, array{label: string, url: string, icon: string, accent: string, external: bool, description?: string}> $hubLinks
     */
    public function __construct(
        private readonly array $profile,
        private readonly array $hubLinks,
    ) {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'profile' => $this->profile,
            'links' => $this->hubLinks,
        ]);
    }
}
