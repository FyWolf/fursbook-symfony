<?php

namespace App\Repository;

use App\Entity\ReportReasons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReportReasons>
 *
 * @method ReportReasons|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportReasons|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportReasons[]    findAll()
 * @method ReportReasons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportReasonsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportReasons::class);
    }

    public function add(ReportReasons $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReportReasons $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReportReasons[] Returns an array of ReportReasons objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReportReasons
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function fetchAllReasons()
    {
        return $this->createQueryBuilder('r')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
