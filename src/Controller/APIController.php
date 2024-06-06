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
    #[Route('/GetNombreCreationsComptes', name: 'app_api_get_nombre_creations_comptes', methods: ['POST'])]
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
    public function GetNombreCreationsComptes(Request $request, EntityManagerInterface $entityManager): JsonResponse
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

    #[Route('/GetNombrePosts', name: 'app_api_get_nombre_posts', methods: ['POST'])]
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
    public function GetNombrePosts(Request $request, EntityManagerInterface $entityManager): JsonResponse
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
            type: 'number'
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
            type: 'number'
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
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'commentCount', type: 'integer'),
                    new OA\Property(property: 'likeCount', type: 'integer'),
                    new OA\Property(property: 'superLikeCount', type: 'integer'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
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
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'commentCount', type: 'integer'),
                    new OA\Property(property: 'likeCount', type: 'integer'),
                    new OA\Property(property: 'superLikeCount', type: 'integer'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
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

    //getMostSuperLikedPosts (5 posts les plus super likés sur une période donnée)
    #[Route('/GetPostsLesPlusSuperLikes', name: 'app_api_get_posts_les_plus_super_likes', methods: ['POST'])]
    #[OA\Tag(name: 'LesPlusSuperLikes')]
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
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'commentCount', type: 'integer'),
                    new OA\Property(property: 'likeCount', type: 'integer'),
                    new OA\Property(property: 'superLikeCount', type: 'integer'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
                ]
            )
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetPostsLesPlusSuperLikes(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $postsLesPlusSuperLikes = $entityManager->getRepository(Post::class)
            ->getMostSuperLikedPosts(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($postsLesPlusSuperLikes);
    }


    // getUsersCommentingTheMost
    #[Route('/GetUtilisateursCommentantLePlus', name: 'app_api_get_utilisateurs_commentant_le_plus', methods: ['POST'])]
    #[OA\Tag(name: 'UtilisateursCommentantLePlus')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'postCount', type: 'integer'),
                    new OA\Property(property: 'commentCount', type: 'integer'),
                    new OA\Property(property: 'followersCount', type: 'integer'),
                    new OA\Property(property: 'followingsCount', type: 'integer')
                ]
            )
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetUtilisateursCommentantLePlus(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $utilisateursCommentantLePlus = $entityManager->getRepository(User::class)
            ->getUsersCommentingTheMost(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($utilisateursCommentantLePlus);
    }

    //getUsersPostingTheMost
    #[Route('/GetUtilisateursPostantLePlus', name: 'app_api_get_utilisateurs_postant_le_plus', methods: ['POST'])]
    #[OA\Tag(name: 'UtilisateursPostantLePlus')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'postCount', type: 'integer'),
                    new OA\Property(property: 'commentCount', type: 'integer'),
                    new OA\Property(property: 'followersCount', type: 'integer'),
                    new OA\Property(property: 'followingsCount', type: 'integer')
                ]
            )
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetUtilisateursPostantLePlus(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $periodeDTO = new Periode_DTO();
        $periodeDTO->dateDebut = new \DateTime($data['dateDebut']);
        $periodeDTO->dateFin = new \DateTime($data['dateFin']);

        $utilisateursPostantLePlus = $entityManager->getRepository(User::class)
            ->getUsersPostingTheMost(
                DateTimeImmutable::createFromMutable($periodeDTO->dateDebut),
                DateTimeImmutable::createFromMutable($periodeDTO->dateFin)
            );

        return $this->json($utilisateursPostantLePlus);
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
