<?php

namespace App\Controller;

use App\Entity\Hashtag;
use App\Entity\Comment;
use App\Entity\Hashtagpc;
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


    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Extract hashtags from the comment body
            $body = $comment->getBody();
            preg_match_all('/#(\w+)/', $body, $matches);
            $newTags = array_unique($matches[1]);

            // Get existing hashtags from the database linked to this comment
            $existingTags = [];
            foreach ($comment->getHashtagpcs() as $hashtagpc) {
                $existingTags[] = $hashtagpc->getHashtag()->getName();
                if (!in_array($hashtagpc->getHashtag()->getName(), $newTags)) {
                    $entityManager->remove($hashtagpc);
                }
            }

            // Add new hashtags
            foreach ($newTags as $tagName) {
                if (!in_array($tagName, $existingTags)) {
                    $hashtag = $entityManager->getRepository(Hashtag::class)->findOneBy(['name' => $tagName]);
                    if (!$hashtag) {
                        $hashtag = new Hashtag();
                        $hashtag->setName($tagName);
                        $entityManager->persist($hashtag);
                    }

                    $hashtagPc = new Hashtagpc();
                    $hashtagPc->setComment($comment);
                    $hashtagPc->setHashtag($hashtag);
                    $entityManager->persist($hashtagPc);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Comment updated successfully.');

            // Redirect to the referrer URL
            $referrer = $request->headers->get('referer');
            return $this->redirect($referrer ? $referrer : $this->generateUrl('app_home'));
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
        if (!($this->getUser() === $comment->getUser() || $this->isGranted('ROLE_ADMIN') || $this->getUser() === $post->getUser())) {
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
