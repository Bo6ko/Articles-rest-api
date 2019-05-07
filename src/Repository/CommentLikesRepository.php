<?php

namespace App\Repository;

use App\Entity\CommentLikes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CommentLikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentLikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentLikes[]    findAll()
 * @method CommentLikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentLikesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CommentLikes::class);
    }

    // /**
    //  * @return CommentLikes[] Returns an array of CommentLikes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentLikes
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findCount($commentId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select('count(c.id)')
            ->Where('c.comment_id = :comment_id')
            ->setParameter('comment_id', $commentId);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findComentLike($userId, $CommentId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select('c.id')
            ->Where('c.user_id = :user_id')
            ->andWhere('c.comment_id = :comment_id')
            ->setParameter('user_id', $userId)
            ->setParameter('comment_id', $CommentId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function deleteLikeFromComment($userId, $CommentId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->delete()
            ->Where('c.user_id = :user_id')
            ->andWhere('c.comment_id = :comment_id')
            ->setParameter('user_id', $userId)
            ->setParameter('comment_id', $CommentId);

        return $qb->getQuery()->getResult();
    }
}
