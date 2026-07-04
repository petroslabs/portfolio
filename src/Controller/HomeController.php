<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\HubLinkRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Landing page (hub central) sur "/".
 *
 * Contenu géré en base de données (App\Entity\Profile, App\Entity\HubLink),
 * éditable depuis l'espace Admin (/admin/profile, /admin/hub-links).
 *
 * Ce contrôleur est volontairement isolé pour cohabiter proprement avec les
 * futurs BlogController, ProjectController et l'espace Admin\.
 */
final class HomeController extends AbstractController
{
    public function __construct(
        private readonly ProfileRepository $profiles,
        private readonly HubLinkRepository $hubLinks,
    ) {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'profile' => $this->profiles->getSingleton(),
            'links' => $this->hubLinks->findAllOrdered(),
        ]);
    }
}
