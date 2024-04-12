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
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the current user is the owner of the user entity
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the current user is the owner of the user entity
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        // Code for deleting the user

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/follow', name: 'app_user_follow', methods: ['GET'])]
public function follow(User $user, EntityManagerInterface $entityManager, FollowRepository $followRepository): Response
{
    // Récupérer l'utilisateur actuel
    $currentUser = $this->getUser();

    // Vérifier si l'utilisateur actuel est authentifié
    if (!$currentUser) {
        // Gérer le cas où l'utilisateur n'est pas authentifié
        // Par exemple, vous pouvez rediriger vers la page de connexion
        $this->addFlash('warning', 'You must be logged in to follow a user.');
        return $this->redirectToRoute('app_login');
    }

    

    // Créer une nouvelle instance de Follow
    $follow = new Follow();
    $follow->setFollowingUser($currentUser);
    $follow->setFollowedUser($user);

    // Persister l'entité Follow
    $entityManager->persist($follow);
    $entityManager->flush();

    return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
}



    

    # Method for unfollowing a user
    #[Route('/{id}/unfollow', name: 'app_user_unfollow', methods: ['GET'])]
    public function unfollow(User $user, EntityManagerInterface $entityManager, FollowRepository $followRepository): Response

    {
        // Récupérer l'utilisateur actuel
        $currentUser = $this->getUser();

        // Récupérer l'entité Follow correspondant à l'utilisateur actuel et à l'utilisateur suivi
        $follow = $followRepository->findOneBy([
            'following_user' => $currentUser,
            'followed_user' => $user,
        ]);

        // Vérifier si l'entité Follow existe
        if ($follow) {
            // Supprimer l'entité Follow
            $entityManager->remove($follow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }
}
