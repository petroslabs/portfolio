<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\UseCategory;
use App\Entity\UseItem;
use App\Form\UseItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/uses/items', name: 'admin_use_item_')]
#[IsGranted('ROLE_ADMIN')]
final class UseItemController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new UseItem();

        if ($categoryId = $request->query->get('category')) {
            $item->setCategory($entityManager->getReference(UseCategory::class, $categoryId));
        }

        $form = $this->createForm(UseItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($item);
            $entityManager->flush();

            $this->addFlash('success', 'Item créé.');

            return $this->redirectToRoute('admin_use_category_index');
        }

        return $this->render('admin/use/item_form.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UseItem $item, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UseItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Item mis à jour.');

            return $this->redirectToRoute('admin_use_category_index');
        }

        return $this->render('admin/use/item_form.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, UseItem $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-use-item-'.$item->getId(), $request->request->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();

            $this->addFlash('success', 'Item supprimé.');
        }

        return $this->redirectToRoute('admin_use_category_index');
    }
}
