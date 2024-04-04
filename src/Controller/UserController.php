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
use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Check if the current user is the owner of the user entity

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the current user is the owner of the user entity
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        // Set password to null to prevent EntityUserProvider from refreshing the user object
        $user->setPassword('');

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
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
