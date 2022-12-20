<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ParameterType;
use App\Entity\ProfileReports;

/**
 * @extends ServiceEntityRepository<ProfileReports>
 *
 * @method ProfileReports|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileReports|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileReports[]    findAll()
 * @method ProfileReports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileReports::class);
    }

    public function add(ProfileReports $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProfileReports $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ProfileReports[] Returns an array of ProfileReports objects
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

//    public function findOneBySomeField($value): ?ProfileReports
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function adminGetReportedUsers($offset)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT profile_reports.id, profile_reports.user_id, profile_reports.profile_id, author.username AS author, target.username AS target, COUNT(*) AS count
                FROM profile_reports
                INNER JOIN user AS author
                ON profile_reports.user_id = author.id
                INNER JOIN user AS target
                ON profile_reports.profile_id = target.id
                GROUP BY profile_reports.profile_id
                LIMIT 15 OFFSET :offset';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('offset', $offset, ParameterType::INTEGER);
        $resultSet = $stmt->execute();
        return $resultSet->fetchAll();
    }

    public function countReportedUsers()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT COUNT(*) FROM profile_reports';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->execute();
        return $resultSet->fetch();
    }
}
