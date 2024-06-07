<?php

namespace App\Controller;

use App\Entity\like;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Report;
use App\Form\PostType;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Hashtag;
use App\Form\ReportType;
use App\Form\CommentType;
use App\Entity\ImagePost;
use App\Entity\Hashtagpc;
use App\Repository\PostRepository;
use App\Repository\LikeRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




#[Route('/post')]
class PostController extends AbstractController
{

    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $post->setCreatedAt(new DateTimeImmutable());

        // Set the user property to the currently logged-in user
        $user = $this->getUser();
        $post->setUser($user);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $body = $post->getBody();
            preg_match_all('/#(\w+)/', $body, $matches);
            $hashtags = array_unique($matches[1]);

            foreach ($hashtags as $tagName) {
                $hashtag = $entityManager->getRepository(Hashtag::class)->findOneBy(['name' => $tagName]);
                if (!$hashtag) {
                    $hashtag = new Hashtag();
                    $hashtag->setName($tagName);
                    $entityManager->persist($hashtag);
                }

                $hashtagPc = new Hashtagpc();
                $hashtagPc->setPost($post);
                $hashtagPc->setHashtag($hashtag);
                $entityManager->persist($hashtagPc);
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $uploadedFiles */
            $uploadedFiles = $form['images']->getData();

            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = preg_replace('/[^a-zA-Z0-9]+/', '_', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                    //try {
                    $uploadedFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    /* } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                        continue;
                    }*/

                    $image = new Image();
                    $image->setImage($newFilename);

                    $imagePost = new ImagePost();
                    $imagePost->setImage($image);
                    $imagePost->setPost($post);

                    $post->addImagePost($imagePost);
                    $entityManager->persist($image);
                    $entityManager->persist($imagePost);
                }
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirect($request->query->get('returnUrl', $this->generateUrl('app_home')));
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    public function show(CommentRepository $commentRepository, Post $post, LikeRepository $likeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {;

        // Récupérer l'entité Like correspondant à l'utilisateur actuel et à l'utilisateur suivi
        $like = $likeRepository->findOneBy([]);
        $superlike = $likeRepository->findOneBy([]);
        $comment = $commentRepository->findOneBy([]);

        $numberOfLikes = $likeRepository->countLikes($post);
        $numberOfComments = $commentRepository->countComments($post);

        $numberOfSuperlikes = count($likeRepository->findBy(['superlike' => $superlike]));
        $numberOfSuperlikes = count($likeRepository->findBy(['post' => $post]));


        // Pass the form view to the template
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'like' => $like,
            'numberOfLikes' => $numberOfLikes,
            'numberOfSuperlikes' => $numberOfSuperlikes,
            'numberOfComments' => $numberOfComments,
        ]);
    }

    #[Route('/status/{id}', name: 'app_single_post', methods: ['GET', 'POST'])]
    public function singlePost(CommentRepository $commentRepository, Post $post, LikeRepository $likeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {;

        // Récupérer l'entité Like correspondant à l'utilisateur actuel et à l'utilisateur suivi
        $like = $likeRepository->findOneBy([]);
        $superlike = $likeRepository->findOneBy([]);
        $comments = $commentRepository->findBy(['post' => $post]);

        $numberOfLikes = $likeRepository->countLikes($post);
        $numberOfComments = $commentRepository->countComments($post);

        $numberOfSuperlikes = count($likeRepository->findBy(['superlike' => $superlike]));
        $numberOfSuperlikes = count($likeRepository->findBy(['post' => $post]));


        // Pass the form view to the template
        return $this->render('post/single-post.html.twig', [
            'post' => $post,
            'like' => $like,
            'numberOfLikes' => $numberOfLikes,
            'numberOfSuperlikes' => $numberOfSuperlikes,
            'numberOfComments' => $numberOfComments,
            'comments' => $comments,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        // Check if the current user is the creator of the post or if the user is an admin
        if ($user !== $post->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Tu n\'es pas autorisé à modifier cette publication.');
            $referer = $request->headers->get('referer');
            $lastPage = $request->getSession()->get('last_page', $this->generateUrl('app_post_index'));
            return $this->redirect($referer ?: $lastPage);
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Extract hashtags from the updated body
            $body = $post->getBody();
            preg_match_all('/#(\w+)/', $body, $matches);
            $newTags = array_unique($matches[1]);

            // Get existing hashtags from the database linked to this post
            $existingTags = [];
            foreach ($post->getHashtagpcs() as $hashtagpc) {
                $existingTags[] = $hashtagpc->getHashtag()->getName();
                if (!in_array($hashtagpc->getHashtag()->getName(), $newTags)) {
                    $entityManager->remove($hashtagpc); // Remove the association if tag no longer exists in body
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
                    $hashtagPc->setPost($post);
                    $hashtagPc->setHashtag($hashtag);
                    $entityManager->persist($hashtagPc);
                }
            }

            // Handling Images
            /** @var UploadedFile[] $uploadedFiles */
            $uploadedFiles = $form['images']->getData();

            // Get existing images
            $existingImages = $post->getImagePosts();

            // Remove existing images that are not part of the new upload
            foreach ($existingImages as $existingImage) {
                $entityManager->remove($existingImage->getImage());
                $entityManager->remove($existingImage);
            }

            // Add new images
            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = preg_replace('/[^a-zA-Z0-9]+/', '_', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                    try {
                        $uploadedFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                        continue;
                    }

                    $image = new Image();
                    $image->setImage($newFilename);

                    $imagePost = new ImagePost();
                    $imagePost->setImage($image);
                    $imagePost->setPost($post);

                    $entityManager->persist($image);
                    $entityManager->persist($imagePost); // Persist the ImagePost entity
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le post a été modifié avec succès.');
            return $this->redirect($request->query->get('returnUrl', $this->generateUrl('app_home')));
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the last page from the session or set default redirection if none is set
        $lastPage = $request->getSession()->get('last_page', $this->generateUrl('app_post_index'));
        $referer = $request->headers->get('referer', $lastPage);

        // Check CSRF token validity for security
        if (!$this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirect($referer);
        }

        // Check if the current user is the creator of the post
        if ($this->getUser() !== $post->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Tu n\'es pas autorisé à supprimer cette publication.');
            return $this->redirect($referer);
        }

        // Proceed with deletion
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'The post has been deleted successfully.');
        return $this->redirect($referer);
    }

    #[Route('/post/{id}/like', name: 'app_post_like', methods: ['GET'])]
    public function like(Request $request, Post $post, EntityManagerInterface $entityManager, LikeRepository $likeRepository): Response
    {
        $referer = $request->headers->get('referer');
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->addFlash('warning', 'Tu dois être connecté pour liker une publication!');
            return $this->redirectToRoute('app_login');
        }

        $like = new Like();
        $like->setSuperlike(false);
        $like->setUser($currentUser);
        $like->setPost($post);
        $like->setCreatedAt(new DateTimeImmutable());

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->redirect($referer);
    }

    #[Route('/post/{id}/unlike', name: 'app_post_unlike', methods: ['GET'])]
    public function unlike(Request $request, Post $post, EntityManagerInterface $entityManager, LikeRepository $likeRepository): Response
    {
        $referer = $request->headers->get('referer');
        $currentUser = $this->getUser();

        $like = $likeRepository->findOneBy([
            'user' => $currentUser,
            'post' => $post,
            'superlike' => false
        ]);

        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
            $this->addFlash('success', 'Like removed successfully.');
        } else {
            $this->addFlash('error', 'No like to remove.');
        }

        return $this->redirect($referer);
    }

    #[Route('/post/{id}/superlike', name: 'app_post_superlike', methods: ['GET'])]
    public function superlike(Request $request, Post $post, EntityManagerInterface $entityManager, LikeRepository $likeRepository): Response
    {
        $referer = $request->headers->get('referer');
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Checking if the user already has an active superlike this week
        $lastSuperlike = $likeRepository->findLastSuperlikeByUser($user);
        if ($lastSuperlike && $lastSuperlike->getCreatedAt() > new \DateTime('-1 week')) {
            $this->addFlash('error', 'Tu ne peux pas superliker plus d\'une fois par semaine!');
            return $this->redirect($referer . '?error=superlike_limit');
        }

        $superlike = new Like();
        $superlike->setUser($user);
        $superlike->setPost($post);
        $superlike->setSuperlike(true);
        $superlike->setCreatedAt(new DateTimeImmutable());


        $entityManager->persist($superlike);
        $entityManager->flush();

        $this->addFlash('success', 'Tu as superliké cette publication!');
        return $this->redirect($referer);
    }

    #[Route('/post/{id}/superunlike', name: 'app_post_superunlike', methods: ['GET'])]
    public function superunlike(Request $request, Post $post, EntityManagerInterface $entityManager, likeRepository $likeRepository): Response
    {
        $referer = $request->headers->get('referer');

        $currentUser = $this->getUser();

        $superlike = $likeRepository->findOneBy([
            'user' => $currentUser,
            'post' => $post,
            'superlike' => true
        ]);

        if ($superlike) {
            $entityManager->remove($superlike);
            $entityManager->flush();
        }

        return $this->redirect($referer);
    }

    #[Route('/post/{id}/reportpost', name: 'app_user_report_post', methods: ['GET', 'POST'])]
    public function reportPost(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        // Fetch the correct Post entity based on the provided ID
        $postReported = $entityManager->getRepository(Post::class)->find($id);

        if (!$postReported) {
            throw $this->createNotFoundException('No post found for id ' . $id);
        }

        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the reporter to the current user
            $report->setUserReporter($this->getUser());
            // Correctly set the reported post
            $report->setPost($postReported);
            $report->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($report);
            $entityManager->flush();

            // Get referrer URL
            $referrer = $request->headers->get('referer');
            if (!$referrer) {
                // Fallback if no referrer is available
                $referrer = $this->generateUrl('homepage');
            }

            // Append 'success=1' to the referrer URL
            $redirectUrl = $referrer . (parse_url($referrer, PHP_URL_QUERY) ? '&' : '?') . 'success=1';
            return $this->redirect($redirectUrl);
        }

        return $this->render('report/_form_post.html.twig', [
            'form' => $form->createView(),
            'post' => $postReported
        ]);
    }

    #[Route('/post/{id}/comment', name: 'app_post_comment', methods: ['POST'])]
    public function comment(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        // Fetch the correct Post entity based on the provided ID
        $postCommented = $entityManager->getRepository(Post::class)->find($id);

        if (!$postCommented) {
            throw $this->createNotFoundException('No post found for id ' . $id);
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setPost($postCommented);
            $comment->setCreatedAt(new \DateTimeImmutable());

            // Extract hashtags from the comment body
            $body = $comment->getBody();
            preg_match_all('/#(\w+)/', $body, $matches);
            $hashtags = array_unique($matches[1]);

            foreach ($hashtags as $tagName) {
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

            $entityManager->persist($comment);
            $entityManager->flush();

            // Get referrer URL
            $referrer = $request->headers->get('referer');
            return $this->redirect($referrer ? $referrer : $this->generateUrl('homepage'));
        }

        // If the form is not submitted or valid, render the form again
        return $this->render('comment/_form.html.twig', [
            'form' => $form->createView(),
            'post' => $postCommented
        ]);
    }
}
