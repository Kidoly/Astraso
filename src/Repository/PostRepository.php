<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchByQuery(string $query)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.title LIKE :query OR p.body LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function countPostsBetweenDates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.created_at BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAverageCommentsPerPost(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): float
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(c.id) as commentCount')
            ->leftJoin('p.comments', 'c')
            ->where('p.created_at BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy('p.id');

        $results = $qb->getQuery()->getScalarResult();

        $totalComments = array_sum(array_column($results, 'commentCount'));
        $numberOfPosts = count($results);

        return $numberOfPosts > 0 ? $totalComments / $numberOfPosts : 0.0;
    }

    public function countAverageLikesPerPost(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): float
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('COUNT(l.id) as likeCount')
            ->leftJoin('p.likes', 'l')
            ->where('p.created_at BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy('p.id');

        $results = $qb->getQuery()->getScalarResult();

        $totalLikes = array_sum(array_column($results, 'likeCount'));
        $numberOfPosts = count($results);

        return $numberOfPosts > 0 ? $totalLikes / $numberOfPosts : 0.0;
    }

    //getMostCommentedPosts (5 posts les plus commentés sur une période donnée)
    public function getMostCommentedPosts(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select(
            '
        p.id,
        p.title,
        p.body,
        p.created_at,
        u.username,
        COUNT(DISTINCT c.id) as commentCount,
        (SELECT COUNT(l1.id) FROM App\Entity\Like l1 WHERE l1.post = p AND l1.superlike = false) as likeCount,
        (SELECT COUNT(l2.id) FROM App\Entity\Like l2 WHERE l2.post = p AND l2.superlike = true) as superlikeCount'
        )
            ->leftJoin('p.comments', 'c')
            ->leftJoin('p.user', 'u')
            ->where('p.created_at BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy('p.id, p.title, p.body, p.created_at, u.username')
            ->orderBy('commentCount', 'DESC')
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }


    //getMostLikedPosts (5 posts les plus likés sur une période donnée)
    public function getMostLikedPosts(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select(
            '
        p.id,
        p.title,
        p.body,
        p.created_at,
        u.username,
        COUNT(DISTINCT c.id) as commentCount,
        (SELECT COUNT(l1.id) FROM App\Entity\Like l1 WHERE l1.post = p AND l1.superlike = false) as likeCount,
        (SELECT COUNT(l2.id) FROM App\Entity\Like l2 WHERE l2.post = p AND l2.superlike = true) as superlikeCount'
        )
            ->leftJoin('p.comments', 'c')
            ->leftJoin('p.user', 'u')
            ->where('p.created_at BETWEEN :start AND :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy('p.id, p.title, p.body, p.created_at, u.username')
            ->orderBy('likeCount', 'DESC')
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }
}
