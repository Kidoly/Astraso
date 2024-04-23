<?php

namespace App\Controller;

use App\Entity\like;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Follow;
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


class FollowController extends AbstractController
{
    #[Route('/follow-hashtag/{id}', name: 'follow_hashtag')]
    public function followHashtag(Request $request, Hashtag $hashtag, EntityManagerInterface $entityManager): Response
    {
        $follow = new Follow();
        $follow->setFollowingUser($this->getUser());
        $follow->setHashtag($hashtag);
        $follow->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($follow);
        $entityManager->flush();

        $this->addFlash('success', 'You are now following the hashtag ' . $hashtag->getName());
        return $this->redirectToRoute('search', ['query' => '#' . $hashtag->getName()]);
    }

    #[Route('/unsubscribe-hashtag/{id}', name: 'unsubscribe_hashtag', methods: ['POST'])]
    public function unsubscribeHashtag(Request $request, Hashtag $hashtag, EntityManagerInterface $entityManager, FollowRepository $followRepository): Response
    {
        $user = $this->getUser();
        if ($user) {
            $follow = $followRepository->findOneBy([
                'hashtag' => $hashtag,
                'following_user' => $user
            ]);

            if ($follow) {
                $entityManager->remove($follow);
                $entityManager->flush();
                $this->addFlash('success', 'You have unsubscribed from the hashtag ' . $hashtag->getName());
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
