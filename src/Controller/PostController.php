<?php

namespace App\Controller;

use App\Entity\like;
use App\Entity\Post;
use App\Entity\Image;
use App\Form\PostType;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\ImagePost;
use App\Repository\PostRepository;
use App\Repository\LikeRepository;
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

        $referer = $request->headers->get('referer');

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $uploadedFiles */
            $uploadedFiles = $form['images']->getData();

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

                    $post->addImagePost($imagePost);
                    $entityManager->persist($image);
                    $entityManager->persist($imagePost);
                }
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirect($referer);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    public function show(Post $post, LikeRepository $likeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        dump('Rendering post/show', $post->getId());
        // Create a new Comment instance
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setUser($this->getUser());

        // Récupérer l'entité Like correspondant à l'utilisateur actuel et à l'utilisateur suivi
        $like = $likeRepository->findOneBy([]);
        $superlike = $likeRepository->findOneBy([]);

        // Récupérer le nombre de personnes suivies par l'utilisateur du compte afficher
        $numberOfLikes = $likeRepository->countLikes($post);

        $numberOfSuperlikes = count($likeRepository->findBy(['superlike' => $superlike]));
        $numberOfSuperlikes = count($likeRepository->findBy(['post' => $post]));

        // Create the form
        $commentForm = $this->createForm(CommentType::class, $comment);
        dump($commentForm->createView());
        dump('Rendering post/show', $commentForm->createView());


        // Pass the form view to the template
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $commentForm->createView(),
            'like' => $like,
            'numberOfLikes' => $numberOfLikes,
            'numberOfSuperlikes' => $numberOfSuperlikes,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the currently logged-in user
        $user = $this->getUser();

        // Check if the current user is the creator of the post
        if ($user !== $post->getUser()) {
            $this->addFlash('error', 'Tu n\'es pas autorisé à modifier cette publication.');
            $referer = $request->headers->get('referer');
            $lastPage = $request->getSession()->get('last_page', $this->generateUrl('app_post_index'));
            return $this->redirect($referer ?: $lastPage);
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $returnUrl = $request->query->get('returnUrl', $this->generateUrl('app_home'));

        if ($form->isSubmitted() && $form->isValid()) {
            // Clear existing images
            foreach ($post->getImagePosts() as $imagePost) {
                $entityManager->remove($imagePost);
                // Optionally delete the image file from the server here
            }
            $entityManager->flush();  // Ensure removal is executed immediately

            // Process new images
            /** @var UploadedFile[] $uploadedFiles */
            $uploadedFiles = $form['images']->getData();
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
                    $entityManager->persist($imagePost);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le post a été modifié avec succès.');
            return $this->redirect($returnUrl);
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
        if ($this->getUser() !== $post->getUser()) {
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
}
