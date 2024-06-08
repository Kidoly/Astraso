<?php

namespace App\Controller;

use App\Entity\Hashtagpc;
use App\Entity\Post;
use App\Entity\Image;
use DateTimeImmutable;
use App\Form\PostType;
use App\Entity\ImagePost;
use App\Repository\PostRepository;
use App\Repository\FollowRepository;
use App\Repository\HashtagpcRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FollowRepository $followRepository, HashtagpcRepository $hashtagpcRepository, PostRepository $postRepository, UserInterface $user): Response
    {
        // Redirect to login page if user is not logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Get the logged-in user
        $currentUser = $this->getUser();

        // Get follows of the current user
        $follows = $followRepository->findBy(['following_user' => $currentUser]);

        // Extract followed users, institutions, and hashtags
        $followedUsers = [];
        $followedInstitutions = [];
        $followedHashtags = [];
        foreach ($follows as $follow) {
            if ($follow->getFollowedUser()) {
                $followedUsers[] = $follow->getFollowedUser()->getId();
            }
            if ($follow->getInstitution()) {
                $followedInstitutions[] = $follow->getInstitution()->getId();
            }
            if ($follow->getHashtag()) {
                $followedHashtags[] = $follow->getHashtag()->getId();
            }
        }

        // Initialize query builder for posts
        $queryBuilder = $postRepository->createQueryBuilder('p');

        // Add conditions based on follows
        if (!empty($followedUsers)) {
            $queryBuilder->orWhere('p.user IN (:followedUsers)')
                ->setParameter('followedUsers', $followedUsers);
        }

        if (!empty($followedInstitutions)) {
            $queryBuilder->orWhere('p.institution IN (:followedInstitutions)')
                ->setParameter('followedInstitutions', $followedInstitutions);
        }

        if (!empty($followedHashtags)) {
            $queryBuilder->orWhere('p.hashtag IN (:followedHashtags)')
                ->setParameter('followedHashtags', $followedHashtags);
        }

        // Set order by creation date
        $queryBuilder->orderBy('p.created_at', 'DESC');

        // Get posts
        $posts = $queryBuilder->getQuery()->getResult();


        if ($posts == []) {
            $posts = $postRepository->findBy([], ['created_at' => 'DESC']);
        }

        $trends = $hashtagpcRepository->findPopularHashtags();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts,
            'user' => $currentUser,
            'trends' => $trends,
        ]);
    }

    #[Route('/explorer', name: 'app_explorer')]
    public function explorer(HashtagpcRepository $hashtagpcRepository, PostRepository $postRepository, UserInterface $user): Response
    {
        // Redirect to login page if user is not logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $posts = $postRepository->findBy([], ['created_at' => 'DESC']);
        $trends = $hashtagpcRepository->findPopularHashtags();

        return $this->render('home/explorer.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts,
            'user' => $user,
            'trends' => $trends,
        ]);
    }
}
