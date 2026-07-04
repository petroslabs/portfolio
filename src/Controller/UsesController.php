<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * "L'établi" sur "/uses" : les outils, la stack et le matériel utilisés au
 * quotidien. Contenu piloté par config/uses.yaml — même pattern que
 * HomeController / ProjectController.
 */
final class UsesController extends AbstractController
{
    /**
     * @param array<int, array{category: array<string,string>|string, items: list<array{name: array<string,string>|string, value: array<string,string>|string}>}> $uses
     */
    public function __construct(
        private readonly array $uses,
    ) {
    }

    #[Route('/uses', name: 'app_uses', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('uses/index.html.twig', [
            'uses' => $this->uses,
        ]);
    }
}
