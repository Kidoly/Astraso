<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\User;
use App\Entity\Image;
use App\Form\UserType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\FollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserController extends AbstractController
{

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, FollowRepository $followRepository, PostRepository $postRepository): Response
    {
        // Return the follow status of the current user

        // Récupérer l'utilisateur actuel
        $currentUser = $this->getUser();

        // Vérifier si l'utilisateur actuel est authentifié
        if ($currentUser) {
            // Récupérer l'entité Follow correspondant à l'utilisateur actuel et à l'utilisateur suivi
            $follow = $followRepository->findOneBy([
                'following_user' => $currentUser,
                'followed_user' => $user,
            ]);

            // Récupérer le nombre de personnes suivies par l'utilisateur du compte afficher
            $numberOfFollowings = count($followRepository->findBy(['following_user' => $user]));

            // Récupérer le nombre de personnes qui suivent l'utilisateur actuel
            $numberOfFollowers = count($followRepository->findBy(['followed_user' => $user]));

            //Réccupérer les nombre de posts de l'utilisateur
            $numberOfPosts = count($postRepository->findBy(['user' => $user]));

            //Afficher les tous les posts de l'utilisateur
            $posts = $postRepository->findBy(['user' => $user]);

            // Passer le résultat à la vue
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'follow' => $follow,
                'numberOfFollowings' => $numberOfFollowings,
                'numberOfFollowers' => $numberOfFollowers,
                'numberOfPosts' => $numberOfPosts,
                'posts' => $posts,
            ]);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();

            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                try {
                    $uploadedFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // TODO: Handle exception
                }

                $image = new Image();
                $image->setImage($newFilename);
                $user->setImage($image);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/user/{id}/follow', name: 'app_user_follow', methods: ['GET'])]
    public function follow(User $user, EntityManagerInterface $entityManager, FollowRepository $followRepository): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->addFlash('warning', 'You must be logged in to follow a user.');
            return $this->redirectToRoute('app_login');
        }

        $follow = new Follow();
        $follow->setFollowingUser($currentUser);
        $follow->setFollowedUser($user);

        $entityManager->persist($follow);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }

    #[Route('/user/{id}/unfollow', name: 'app_user_unfollow', methods: ['GET'])]
    public function unfollow(User $user, EntityManagerInterface $entityManager, FollowRepository $followRepository): Response
    {
        $currentUser = $this->getUser();

        $follow = $followRepository->findOneBy([
            'following_user' => $currentUser,
            'followed_user' => $user,
        ]);

        if ($follow) {
            $entityManager->remove($follow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }
}
