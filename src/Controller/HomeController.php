<?php

namespace App\Controller;

use App\Entity\Hashtagpc;
use App\Entity\Post;
use App\Entity\Image;
use DateTimeImmutable;
use App\Form\PostType;
use App\Entity\ImagePost;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\HashtagpcRepository;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(HashtagpcRepository $hashtagpcRepository, PostRepository $postRepository, UserInterface $user): Response
    {
        // Redirect to login page if user is not logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $posts = $postRepository->findBy([], ['created_at' => 'DESC']);
        $trends = $hashtagpcRepository->findPopularHashtags();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts,
            'user' => $user,
            'trends' => $trends,
        ]);
    }
}
