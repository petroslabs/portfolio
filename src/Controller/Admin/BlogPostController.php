<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Form\BlogPostType;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/blog', name: 'admin_blog_post_')]
#[IsGranted('ROLE_ADMIN')]
final class BlogPostController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(BlogPostRepository $posts): Response
    {
        return $this->render('admin/blog_post/index.html.twig', [
            'posts' => $posts->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé.');

            return $this->redirectToRoute('admin_blog_post_index');
        }

        return $this->render('admin/blog_post/form.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BlogPost $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Article mis à jour.');

            return $this->redirectToRoute('admin_blog_post_index');
        }

        return $this->render('admin/blog_post/form.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, BlogPost $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete-blog-post-'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();

            $this->addFlash('success', 'Article supprimé.');
        }

        return $this->redirectToRoute('admin_blog_post_index');
    }
}
