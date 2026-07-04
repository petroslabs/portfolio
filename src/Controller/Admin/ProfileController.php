<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Profil du hub : singleton, édition seule (pas de création/suppression).
 */
#[Route('/admin/profile', name: 'admin_profile_edit', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_ADMIN')]
final class ProfileController extends AbstractController
{
    public function __invoke(Request $request, ProfileRepository $profiles, EntityManagerInterface $entityManager): Response
    {
        $profile = $profiles->getSingleton();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour.');

            return $this->redirectToRoute('admin_profile_edit');
        }

        return $this->render('admin/profile/form.html.twig', [
            'form' => $form,
        ]);
    }
}
