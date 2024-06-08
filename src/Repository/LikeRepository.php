<?php

namespace App\Repository;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    //    /**
    //     * @return Like[] Returns an array of Like objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Like
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countLikes(Post $post): int
    {
        return $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->where('l.post = :post')
            ->andWhere('l.superlike = false')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countSuperLikes(Post $post): int
    {
        return $this->createQueryBuilder('l')
            ->select('count(l.id)')
            ->where('l.post = :post')
            ->andWhere('l.superlike = true')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findLastSuperlikeByUser(User $user): ?Like
    {
        return $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->andWhere('l.superlike = true')
            ->setParameter('user', $user)
            ->orderBy('l.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
