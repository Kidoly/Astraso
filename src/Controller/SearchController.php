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
use App\Repository\UserRepository;
use App\Repository\FollowRepository;
use App\Repository\HashtagRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request, PostRepository $postRepository, UserRepository $userRepository, HashtagRepository $hashtagRepository, FollowRepository $followRepository): Response
    {
        $query = $request->query->get('query');
        if (!$query) {
            return $this->render('search/index.html.twig', ['error' => 'No search term provided']);
        }

        $isHashtagSearch = str_starts_with($query, '#');
        $posts = $postRepository->searchByQuery($query);
        $users = $userRepository->searchByQuery($query);
        $followingHashtag = null;
        $hashtag = null;

        if ($isHashtagSearch) {
            $hashtagName = substr($query, 1); // Remove the '#' character
            $hashtag = $hashtagRepository->findOneByName($hashtagName);
            if ($hashtag && $this->getUser()) {
                $followingHashtag = $followRepository->findOneBy([
                    'hashtag' => $hashtag,
                    'following_user' => $this->getUser()
                ]);
            }
        }

        return $this->render('search/index.html.twig', [
            'posts' => $posts,
            'users' => $users,
            'query' => $query,
            'hashtag' => $hashtag,
            'isFollowingHashtag' => $followingHashtag ? true : false
        ]);
    }
}
