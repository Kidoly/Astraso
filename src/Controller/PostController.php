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
                        // Handle the exception appropriately
                    }

                    $image = new Image();
                    $image->setImage($newFilename);

                    $imagePost = new ImagePost();
                    $imagePost->setImage($image);
                    $imagePost->setPost($post);

                    $post->addImagePost($imagePost);
                    $entityManager->persist($image);
                    $entityManager->persist($imagePost); // Persist the ImagePost entity
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

        // Récupérer le nombre de personnes suivies par l'utilisateur du compte afficher
        $numberOfLikes = count($likeRepository->findBy(['like' => $post]));

        $numberOfLikes = count($likeRepository->findBy(['like' => $like]));

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
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the currently logged-in user
        $user = $this->getUser();

        // Check if the current user is the creator of the post
        if ($user !== $post->getUser()) {
            $this->addFlash('error', 'You are not authorized to edit this post.');
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
                        $this->addFlash('error', 'Failed to upload image.');
                        continue;
                    }

                    $image = new Image();
                    $image->setImage($newFilename);

                    $imagePost = new ImagePost();
                    $imagePost->setImage($image);
                    $imagePost->setPost($post);

                    $entityManager->persist($image);
                    $entityManager->persist($imagePost);  // Ensure new ImagePost is also persisted
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Post updated successfully.');
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
            $this->addFlash('error', 'You are not authorized to delete this post.');
            return $this->redirect($referer);
        }

        // Proceed with deletion
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post deleted successfully.');
        return $this->redirect($referer);
    }

    #[Route('/post/{id}/like', name: 'app_post_like', methods: ['GET'])]
    public function like(Request $request, Post $post, EntityManagerInterface $entityManager, LikeRepository $likeRepository): Response
    {
        $referer = $request->headers->get('referer');
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->addFlash('warning', 'You must be logged in to like a post.');
            return $this->redirectToRoute('app_login');
        }

        $like = new Like();
        $like->setSuperlike(false); // Attention : le superlike est désactivé par défaut
        $like->setUser($currentUser);
        $like->setPost($post);
        $like->setCreatedAt(new DateTimeImmutable());

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->redirect($referer);
    }

    #[Route('/post/{id}/unlike', name: 'app_post_unlike', methods: ['GET'])]
    public function unlike(Request $request, Post $post, EntityManagerInterface $entityManager, likeRepository $likeRepository): Response
    {
        $referer = $request->headers->get('referer');

        $currentUser = $this->getUser();

        $like = $likeRepository->findOneBy([
            'user' => $currentUser,
            'post' => $post,
        ]);

        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
        }

        return $this->redirect($referer);
    }
}
