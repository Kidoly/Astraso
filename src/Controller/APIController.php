<?php

namespace App\Controller;

use App\DTO\Periode_DTO;
use phpDocumentor\Reflection\Types\Integer;
use App\Repository\UserRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Post;

#[Route('/api')]
class APIController extends AbstractController
{
    #[Route('/GetNombreCreationsCompte', name: 'app_api_get_nombre_creations_compte', methods: ['POST'])]
    #[OA\Tag(name: 'General')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetNombreCreationsCompte(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $nombreCreations = $entityManager->getRepository(User::class)
            ->countCreationsBetweenDates(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($nombreCreations);
    }

    #[Route('/GetNombrePost', name: 'app_api_get_nombre_post', methods: ['POST'])]
    #[OA\Tag(name: 'General')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetNombrePost(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $nombrePost = $entityManager->getRepository(Post::class)
            ->countPostsBetweenDates(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($nombrePost);
    }

    //Nombre moyen de commentaire par post
    #[Route('/GetNombreMoyenCommentaireParPost', name: 'app_api_get_nombre_moyen_commentaire_par_post', methods: ['POST'])]
    #[OA\Tag(name: 'General')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetNombreMoyenCommentaireParPost(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $nombreMoyenCommentaireParPost = $entityManager->getRepository(Post::class)
            ->countAverageCommentsPerPost(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($nombreMoyenCommentaireParPost);
    }


    #[Route('/GetNombreMoyenLikeParPost', name: 'app_api_get_nombre_moyen_like_par_post', methods: ['POST'])]
    #[OA\Tag(name: 'General')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetNombreMoyenLikeParPost(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $nombreMoyenLikeParPost = $entityManager->getRepository(Post::class)
            ->countAverageLikesPerPost(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($nombreMoyenLikeParPost);
    }

    /*#[Route('/RetourneUnePeriodeAleatoire', name:'app_api_periode', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type:Periode_DTO::class)
    )]
    public function RetourneUnePeriodeAleatoire(): Response
    {
        $periode = new Periode_DTO();
        $periode->dateDebut = new \DateTime();
        $periode->dateFin = new \DateTime();
        return $this->json($periode);
    }*/
}
