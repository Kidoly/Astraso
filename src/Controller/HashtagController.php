<?php

namespace App\Controller;

use App\Entity\Hashtag;
use App\Form\HashtagType;
use App\Repository\HashtagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/hashtag')]
class HashtagController extends AbstractController
{
    #[Route('/', name: 'app_hashtag_index', methods: ['GET'])]
    public function index(HashtagRepository $hashtagRepository): Response
    {
        return $this->render('hashtag/index.html.twig', [
            'hashtags' => $hashtagRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hashtag_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hashtag = new Hashtag();
        $form = $this->createForm(HashtagType::class, $hashtag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hashtag);
            $entityManager->flush();

            return $this->redirectToRoute('app_hashtag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hashtag/new.html.twig', [
            'hashtag' => $hashtag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hashtag_show', methods: ['GET'])]
    public function show(Hashtag $hashtag): Response
    {
        return $this->render('hashtag/show.html.twig', [
            'hashtag' => $hashtag,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hashtag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hashtag $hashtag, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HashtagType::class, $hashtag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hashtag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hashtag/edit.html.twig', [
            'hashtag' => $hashtag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hashtag_delete', methods: ['POST'])]
    public function delete(Request $request, Hashtag $hashtag, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hashtag->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($hashtag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hashtag_index', [], Response::HTTP_SEE_OTHER);
    }
}
