<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Code for creating a new user
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Check if the current user is the owner of the user entity
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the current user is the owner of the user entity
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        // Code for editing the user
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the current user is the owner of the user entity
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        // Code for deleting the user
    }

    # Method for following a user
    #[Route('/{id}/follow', name: 'app_user_follow', methods: ['GET'])]
    public function follow(User $user, EntityManagerInterface $entityManager, Follow $follow): Response
    {
        $follow->setFollowingUser($this->getUser());

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    # Method for unfollowing a user
    #[Route('/{id}/unfollow', name: 'app_user_unfollow', methods: ['GET'])]
    public function unfollow(User $user, EntityManagerInterface $entityManager, Follow $follow): Response
    {
        $follow->setFollowingUser($this->getUser());

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
