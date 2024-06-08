<?php

namespace App\Repository;

use App\Entity\Hashtagpc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hashtagpc>
 *
 * @method Hashtagpc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hashtagpc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hashtagpc[]    findAll()
 * @method Hashtagpc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HashtagpcRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hashtagpc::class);
    }

    //    /**
    //     * @return Hashtagpc[] Returns an array of Hashtagpc objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Hashtagpc
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    // A custom method to find 5 most popular hashtags (by number of posts)
    public function findPopularHashtags(): array
    {
        return $this->createQueryBuilder('h')
            ->select('ht.name, COUNT(h.id) as postCount')
            ->join('h.hashtag', 'ht')
            ->groupBy('ht.name')
            ->orderBy('postCount', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
