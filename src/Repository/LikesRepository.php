<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use App\Entity\Likes;

/**
 * @extends ServiceEntityRepository<Likes>
 *
 * @method Likes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likes[]    findAll()
 * @method Likes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Likes $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Likes $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Likes[] Returns an array of Likes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Likes
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function checkIfLiked($postId, $userId): ?Likes
    {
        return $this->createQueryBuilder('l')
        ->andWhere('l.post_id = :postId')
        ->andWhere('l.user_id = :userId')
        ->setParameter('postId', $postId)
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }

    public function countLikes($postId)
    {
        return $this->createQueryBuilder('l')
        ->andWhere('l.post_id = :postId')
        ->select('count(l.id)')
        ->setParameter('postId', $postId)
        ->getQuery()
        ->getSingleScalarResult()
        ;
    }

    public function deleteLike($postId, $userId)
    {
        return $this->createQueryBuilder('l')
        ->delete('likes', 'likes')
        ->where('l.post_id = :postId')
        ->andWhere('l.user_id = :userId')
        ->setParameter('postId', $postId)
        ->setParameter('userId', $userId)
        ->execute()
        ;
    }
}
