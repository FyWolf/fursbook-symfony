<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use App\Entity\Posts;
use App\Entity\Likes;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Posts>
 *
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Posts $entity, bool $flush = true): void
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
    public function remove(Posts $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Posts[] Returns an array of Posts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findAllPostsById($id, $offset)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.Owner = :id')
            ->setParameter('id', $id)
            ->orderBy('u.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->setFirstResult($offset)
            ->getResult()
        ;
    }

    public function findAllPosts($offset)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->setFirstResult($offset)
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Posts
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAllPosts($doctrine, $start, $loggedUser) {
        $userRepo = $doctrine->getRepository(User::class);

        $foundPosts = $this->findAllPosts($start);
        $resultPosts = [];

        foreach ($foundPosts as $result) {
            $likeRepos = $doctrine->getRepository(Likes::class);
            $user = $userRepo->findOneBy(['id' => $result->getOwner()]);

            if($loggedUser){
                $foundLike = $likeRepos->checkIfLiked($result->getId(), $loggedUser->getId());
                if($foundLike) {
                    $liked = true;
                }
                else {
                    $liked = false;
                }
            }
            else {
                $liked = false;
            }

            $countLike = $likeRepos->countLikes($result->getId());
            $constructedResult = (object) [
                'ownerProfilePicture' => $user->getProfilePicture(),
                'ownerUsername' => $user->getUsername(),
                'postId' => $result->getId(),
                'isLiked' => $liked,
                'nbLikes' => $countLike,
                'content' => $result->getContent(),
                'nbPictures' => $result->getNbPictures(),
                'picture1' => $result->getPicture1(),
                'picture2' => $result->getPicture2(),
                'picture3' => $result->getPicture3(),
                'picture4' => $result->getPicture4(),
                'date' => date('h:i d M Y', intval($result->getDatePosted())),
            ];

            array_push($resultPosts, $constructedResult);
        }

        return($resultPosts);
    }

    public function getUserPosts($doctrine, $showedUser, $start, $loggedUser) {
        $userRepo = $doctrine->getRepository(User::class);

        $foundPosts = $this->findAllPostsById($showedUser->getId(), $start);
        $resultPosts = [];

        foreach ($foundPosts as $result) {
            $likeRepos = $doctrine->getRepository(Likes::class);
            $user = $userRepo->findOneBy(['id' => $result->getOwner()]);

            if($loggedUser){
                $foundLike = $likeRepos->checkIfLiked($result->getId(), $loggedUser->getId());
                if($foundLike) {
                    $liked = true;
                }
                else {
                    $liked = false;
                }
            }
            else {
                $liked = false;
            }

            $countLike = $likeRepos->countLikes($result->getId());
            $constructedResult = (object) [
                'ownerProfilePicture' => $user->getProfilePicture(),
                'ownerUsername' => $user->getUsername(),
                'postId' => $result->getId(),
                'isLiked' => $liked,
                'nbLikes' => $countLike,
                'content' => $result->getContent(),
                'nbPictures' => $result->getNbPictures(),
                'picture1' => $result->getPicture1(),
                'picture2' => $result->getPicture2(),
                'picture3' => $result->getPicture3(),
                'picture4' => $result->getPicture4(),
                'date' => date('h:i d M Y', intval($result->getDatePosted())),
            ];

            array_push($resultPosts, $constructedResult);
        }

        return($resultPosts);
    }
}
