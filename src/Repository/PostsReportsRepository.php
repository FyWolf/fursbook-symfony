<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ParameterType;
use App\Entity\PostsReports;

/**
 * @extends ServiceEntityRepository<PostsReports>
 *
 * @method PostsReports|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostsReports|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostsReports[]    findAll()
 * @method PostsReports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostsReports::class);
    }

    public function add(PostsReports $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PostsReports $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PostsReports[] Returns an array of PostsReports objects
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

//    public function findOneBySomeField($value): ?PostsReports
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function adminGetReportedPosts($offset)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT posts_reports.*, author.username AS author, target.username AS target, COUNT(*) AS count
                FROM posts_reports
                INNER JOIN user AS author
                ON posts_reports.user_id = author.id
                INNER JOIN posts AS post
                ON posts_reports.post_id = post.id
                INNER JOIN user AS target
                ON post.owner_id = target.id
                GROUP BY posts_reports.post_id
                LIMIT 15 OFFSET :offset';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('offset', $offset, ParameterType::INTEGER);
        $resultSet = $stmt->execute();
        return $resultSet->fetchAll();
    }

    public function countReportedPosts()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT COUNT(*) FROM posts_reports';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->execute();
        return $resultSet->fetch();
    }

    public function selectAllReportById($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT *
                FROM posts_reports
                WHERE posts_reports.post_id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id, ParameterType::INTEGER);
        $resultSet = $stmt->execute();
        return $resultSet->fetchAll();
    }

    public function selectReportById($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT target.username AS targetUsername, author.username AS authorUsername, posts_reports.*
                FROM posts_reports
                INNER JOIN posts AS post
                ON posts_reports.post_id = post.id
                INNER JOIN user AS target
                ON post.owner_id = target.id
                INNER JOIN user AS author
                ON posts_reports.user_id = author.id
                WHERE posts_reports.post_id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id, ParameterType::INTEGER);
        $resultSet = $stmt->execute();
        return $resultSet->fetch();
    }
}
