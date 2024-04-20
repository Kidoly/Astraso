<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Follow;
use App\Entity\Institution;
use App\Form\InstitutionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstitutionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/institution')]
class InstitutionController extends AbstractController
{
    #[Route('/', name: 'app_institution_index', methods: ['GET'])]
    public function index(InstitutionRepository $institutionRepository): Response
    {
        $user = $this->getUser();
        $institutions = $institutionRepository->findAll();
        $follows = [];

        foreach ($institutions as $institution) {
            $isFollowing = false;
            foreach ($institution->getFollows() as $follow) {
                if ($follow->getFollowingUser() === $user) {
                    $isFollowing = true;
                    break;
                }
            }
            $follows[$institution->getId()] = $isFollowing;
        }

        return $this->render('institution/index.html.twig', [
            'institutions' => $institutions,
            'follows' => $follows,
        ]);
    }


    #[Route('/new', name: 'app_institution_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $institution = new Institution();
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($institution);
            $entityManager->flush();

            return $this->redirectToRoute('app_institution_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('institution/new.html.twig', [
            'institution' => $institution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_institution_show', methods: ['GET'])]
    public function show(Institution $institution): Response
    {
        return $this->render('institution/show.html.twig', [
            'institution' => $institution,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_institution_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Institution $institution, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_institution_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('institution/edit.html.twig', [
            'institution' => $institution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_institution_delete', methods: ['POST'])]
    public function delete(Request $request, Institution $institution, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $institution->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($institution);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_institution_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/follow/{id}', name: 'app_institution_follow', methods: ['POST'])]
    public function follow(Institution $institution, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $follow = new Follow();
        $follow->setFollowingUser($user);
        $follow->setInstitution($institution);
        $entityManager->persist($follow);
        $entityManager->flush();

        return $this->redirectToRoute('app_institution_index');
    }

    #[Route('/unfollow/{id}', name: 'app_institution_unfollow', methods: ['POST'])]
    public function unfollow(Institution $institution, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $follow = $entityManager->getRepository(Follow::class)->findOneBy([
            'following_user' => $user,
            'institution' => $institution
        ]);

        if ($follow) {
            $entityManager->remove($follow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_institution_index');
    }
}
