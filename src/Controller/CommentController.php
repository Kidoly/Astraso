<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Comment updated successfully.');

            // Get referrer URL
            $referrer = $request->headers->get('referer');
            if (!$referrer) {
                // Fallback if no referrer is available
                $referrer = $this->generateUrl('homepage');
            }

            // Redirect to the referrer URL
            return $this->redirect($referrer);
        }

        return $this->render('comment/edit_modal_form.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $post = $comment->getPost();
        // Retrieve the last page from the session or set default redirection if none is set
        $lastPage = $request->getSession()->get('last_page', $this->generateUrl('app_post_index'));
        $referer = $request->headers->get('referer', $lastPage);

        // Check CSRF token validity for security
        if (!$this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirect($referer);
        }

        // Check if the current user is the creator of the comment or an admin or the creator of the post
        if ($this->getUser() !== $comment->getUser() or !$this->isGranted('ROLE_ADMIN') or $this->getUser() !== $post->getUser()) {
            $this->addFlash('error', 'Tu n\'es pas autorisé à supprimer cette publication.');
            return $this->redirect($referer);
        }

        // Proceed with deletion
        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'The post has been deleted successfully.');
        return $this->redirect($referer);
    }
}
