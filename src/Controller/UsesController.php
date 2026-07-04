<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UseCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * "L'établi" sur "/uses" : les outils, la stack et le matériel utilisés au
 * quotidien. Contenu géré en base de données (App\Entity\UseCategory /
 * UseItem), éditable depuis l'espace Admin (/admin/uses).
 */
final class UsesController extends AbstractController
{
    public function __construct(
        private readonly UseCategoryRepository $categories,
    ) {
    }

    #[Route('/uses', name: 'app_uses', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('uses/index.html.twig', [
            'uses' => $this->categories->findAllOrdered(),
        ]);
    }
}
