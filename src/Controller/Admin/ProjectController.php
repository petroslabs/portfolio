<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/projects', name: 'admin_project_')]
#[IsGranted('ROLE_ADMIN')]
final class ProjectController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ProjectRepository $projects): Response
    {
        return $this->render('admin/project/index.html.twig', [
            'projects' => $projects->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet créé.');

            return $this->redirectToRoute('admin_project_index');
        }

        return $this->render('admin/project/form.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Projet mis à jour.');

            return $this->redirectToRoute('admin_project_index');
        }

        return $this->render('admin/project/form.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-project-'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet supprimé.');
        }

        return $this->redirectToRoute('admin_project_index');
    }
}
