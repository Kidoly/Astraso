<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\FollowRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, FollowRepository $followRepository, PostRepository $postRepository): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser) {
            $follow = $followRepository->findOneBy([
                'following_user' => $currentUser,
                'followed_user' => $user,
            ]);

            $numberOfFollowings = $followRepository->count(['following_user' => $user]);
            $numberOfFollowers = $followRepository->count(['followed_user' => $user]);
            $numberOfPosts = $postRepository->count(['user' => $user]);
            $posts = $postRepository->findBy(['user' => $user]);

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

    #[Route('/user/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
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
