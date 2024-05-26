<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchByQuery(string $query)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :query OR u.email LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function countCreationsBetweenDates(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): int
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('COUNT(u.id)')
            ->where('u.createdAt >= :start AND u.createdAt <= :end')
            ->setParameter('start', $startDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->setParameter('end', $endDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE);

        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function getUsersPostingTheMost(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select([
            'u.id',
            'u.username',
            'COUNT(DISTINCT p.id) as postCount',
            'COUNT(DISTINCT c.id) as commentCount',
            '(SELECT COUNT(f1.id) FROM App\Entity\Follow f1 WHERE f1.followed_user = u) as followersCount',
            '(SELECT COUNT(f2.id) FROM App\Entity\Follow f2 WHERE f2.following_user = u) as followingsCount'
        ])
            ->leftJoin('u.posts', 'p')
            ->leftJoin('u.comments', 'c')
            ->where('p.created_at BETWEEN :start AND :end')
            ->setParameter('start', $startDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->setParameter('end', $endDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->groupBy('u.id')
            ->orderBy('postCount', 'DESC')
            ->setMaxResults(5); // Limit the results to the top 5

        try {
            return $qb->getQuery()->getResult();
        } catch (\Exception $e) {
            // Log the error or handle it as per your error handling policy
            throw new \RuntimeException('Error retrieving users posting the most: ' . $e->getMessage());
        }
    }


    //getUsersCommentingTheMost (5 utilisateurs ayant commenté le plus sur une période donnée), id, username, postCount, commentCount, followersCount, followingsCount
    public function getUsersCommentingTheMost(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select([
            'u.id',
            'u.username',
            'COUNT(DISTINCT p.id) as postCount',
            'COUNT(DISTINCT c.id) as commentCount',
            '(SELECT COUNT(f1.id) FROM App\Entity\Follow f1 WHERE f1.followed_user = u) as followersCount',
            '(SELECT COUNT(f2.id) FROM App\Entity\Follow f2 WHERE f2.following_user = u) as followingsCount'
        ])
            ->leftJoin('u.posts', 'p')
            ->leftJoin('u.comments', 'c')
            ->where('p.created_at BETWEEN :start AND :end')
            ->setParameter('start', $startDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->setParameter('end', $endDate, \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->groupBy('u.id')
            ->orderBy('commentCount', 'DESC')
            ->setMaxResults(5); // Limit the results to the top 5

        try {
            return $qb->getQuery()->getResult();
        } catch (\Exception $e) {
            // Log the error or handle it as per your error handling policy
            throw new \RuntimeException('Error retrieving users posting the most: ' . $e->getMessage());
        }
    }
}
