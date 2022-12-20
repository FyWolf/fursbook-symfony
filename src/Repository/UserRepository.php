<?php

namespace App\Repository;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\ORMException;
use App\Entity\User;

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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
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
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByUsername($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :username')
            ->setParameter('username', $value)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByEmail($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :mail')
            ->setParameter('mail', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function adminGetUsers($offset)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT user.* FROM user LIMIT 15 OFFSET :offset';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('offset', $offset, ParameterType::INTEGER);
        $resultSet = $stmt->execute();
        return $resultSet->fetchAll();
    }

    public function countUsers()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT COUNT(*) FROM user';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->execute();
        return $resultSet->fetch();
    }

    public function selectUserViaID($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT user.* FROM user WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id, ParameterType::INTEGER);
        $resultSet = $stmt->execute();
        return $resultSet->fetch();
    }

    public function setEmailViaID($id, $email, $pass)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'UPDATE user SET email = :email, password = :pass WHERE user.id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id, ParameterType::INTEGER);
        $stmt->bindParam('email', $email, ParameterType::STRING);
        $stmt->bindParam('pass', $pass, ParameterType::STRING);
        $stmt->execute();
    }

    public function setUsernameViaID($id, $username)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'UPDATE user SET username = :username WHERE user.id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id, ParameterType::INTEGER);
        $stmt->bindParam('username', $username, ParameterType::STRING);
        $stmt->execute();
    }

    public function deleteUserViaID($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'DELETE FROM user WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('id', $id, ParameterType::INTEGER);
        $stmt->execute();
    }

    public function adminCreateUser($email, $password, $username, $pfp, $bio, $banner)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'INSERT INTO user (email, password, username, profile_picture, bio, profile_banner, is_verified, creation_date) VALUES (:email, :password, :username, :pfp, :bio, :banner, 0, :date)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('email', $email, ParameterType::STRING);
        $stmt->bindParam('password', $password, ParameterType::STRING);
        $stmt->bindParam('username', $username, ParameterType::STRING);
        $stmt->bindParam('pfp', $pfp, ParameterType::STRING);
        $stmt->bindParam('bio', $bio, ParameterType::STRING);
        $stmt->bindParam('banner', $banner, ParameterType::STRING);
        $time = time();
        $stmt->bindParam('date', $time, ParameterType::INTEGER);
        $resultSet = $stmt->execute();
    }

    public function checkUsername($username)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT user.* FROM user WHERE username = :username';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('username', $username, ParameterType::STRING);
        $resultSet = $stmt->execute();
        return $resultSet->fetch();
    }

    public function checkEmail($mail)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT user.* FROM user WHERE email = :mail';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam('mail', $mail, ParameterType::STRING);
        $resultSet = $stmt->execute();
        return $resultSet->fetch();
    }

    public function getSubscribedUsers()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isSubscribed = 1')
            ->getQuery()
            ->getResult()
        ;
    }
}

