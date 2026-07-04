<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\HubLink;
use App\Form\HubLinkType;
use App\Repository\HubLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/hub-links', name: 'admin_hub_link_')]
#[IsGranted('ROLE_ADMIN')]
final class HubLinkController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(HubLinkRepository $hubLinks): Response
    {
        return $this->render('admin/hub_link/index.html.twig', [
            'hubLinks' => $hubLinks->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hubLink = new HubLink();
        $form = $this->createForm(HubLinkType::class, $hubLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hubLink);
            $entityManager->flush();

            $this->addFlash('success', 'Lien créé.');

            return $this->redirectToRoute('admin_hub_link_index');
        }

        return $this->render('admin/hub_link/form.html.twig', [
            'hubLink' => $hubLink,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HubLink $hubLink, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HubLinkType::class, $hubLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Lien mis à jour.');

            return $this->redirectToRoute('admin_hub_link_index');
        }

        return $this->render('admin/hub_link/form.html.twig', [
            'hubLink' => $hubLink,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, HubLink $hubLink, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-hub-link-'.$hubLink->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hubLink);
            $entityManager->flush();

            $this->addFlash('success', 'Lien supprimé.');
        }

        return $this->redirectToRoute('admin_hub_link_index');
    }
}
