<?php

namespace App\Repository;

use App\Entity\Newsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Newsletter>
 *
 * @method Newsletter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Newsletter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Newsletter[]    findAll()
 * @method Newsletter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsletter::class);
    }

    public function add(Newsletter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Newsletter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllNews($offset)
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->setFirstResult($offset)
            ->getResult()
        ;
    }

    public function getLastNews()
    {
        $result = $this->createQueryBuilder('n')
            ->orderBy('n.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if($result)
        {
            $resultNews = (object) [
                'newsId' => $result->getId(),
                'title' => $result->getTitle(),
                'content' => $result->getContent(),
                'date' => strftime('%R %d %b %Y', intval($result->getDate())),
            ];
            return $resultNews;
        }else {
            return null;
        }
    }

    public function getAllNews($doctrine, $start) {
        $foundNews = $this->findAllNews($start);
        $resultNews = [];

        foreach ($foundNews as $result) {
            $constructedResult = (object) [
                'newsId' => $result->getId(),
                'title' => $result->getTitle(),
                'content' => $result->getContent(),
                'date' => strftime('%R %d %b %Y', intval($result->getDate())),
            ];
            array_push($resultNews, $constructedResult);
        }
        return $resultNews;
    }

//    /**
//     * @return Newsletter[] Returns an array of Newsletter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Newsletter
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
