<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\UseCategory;
use App\Form\UseCategoryType;
use App\Repository\UseCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/uses', name: 'admin_use_category_')]
#[IsGranted('ROLE_ADMIN')]
final class UseCategoryController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(UseCategoryRepository $categories): Response
    {
        return $this->render('admin/use/index.html.twig', [
            'categories' => $categories->findAllOrdered(),
        ]);
    }

    #[Route('/categories/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new UseCategory();
        $form = $this->createForm(UseCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie créée.');

            return $this->redirectToRoute('admin_use_category_index');
        }

        return $this->render('admin/use/category_form.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UseCategory $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UseCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie mise à jour.');

            return $this->redirectToRoute('admin_use_category_index');
        }

        return $this->render('admin/use/category_form.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, UseCategory $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-use-category-'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie supprimée (avec ses items).');
        }

        return $this->redirectToRoute('admin_use_category_index');
    }
}
