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

    //getMostCommentedPosts (5 posts les plus commentés sur une période donnée)
    #[Route('/GetPostsLesPlusCommentes', name: 'app_api_get_posts_les_plus_commentes', methods: ['POST'])]
    #[OA\Tag(name: 'LesPlusCommentes')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'body', type: 'string'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'commentCount', type: 'integer'),
                    new OA\Property(property: 'likeCount', type: 'integer'),
                    new OA\Property(property: 'superLikeCount', type: 'integer')
                ]
            )
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetPostsLesPlusCommentes(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $postsLesPlusCommentes = $entityManager->getRepository(Post::class)
            ->getMostCommentedPosts(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($postsLesPlusCommentes);
    }

    //getMostLikedPosts (5 posts les plus likés sur une période donnée)
    #[Route('/GetPostsLesPlusLikes', name: 'app_api_get_posts_les_plus_likes', methods: ['POST'])]
    #[OA\Tag(name: 'LesPlusLikes')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'body', type: 'string'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'commentCount', type: 'integer'),
                    new OA\Property(property: 'likeCount', type: 'integer'),
                    new OA\Property(property: 'superLikeCount', type: 'integer')
                ]
            )
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetPostsLesPlusLikes(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $postsLesPlusLikes = $entityManager->getRepository(Post::class)
            ->getMostLikedPosts(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($postsLesPlusLikes);
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
