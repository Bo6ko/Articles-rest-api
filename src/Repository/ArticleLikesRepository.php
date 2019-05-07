<?php

namespace App\Repository;

use App\Entity\ArticleLikes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArticleLikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleLikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleLikes[]    findAll()
 * @method ArticleLikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleLikesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArticleLikes::class);
    }

    // /**
    //  * @return ArticleLikes[] Returns an array of ArticleLikes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArticleLikes
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findCount($articleId)
    {
        $qb = $this->createQueryBuilder('l');
        $qb
            ->select('count(l.id)')
            ->Where('l.article_id = :article_id')
            ->setParameter('article_id', $articleId);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findArticleLike($userId, $articleid)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select('a.id')
            ->Where('a.user_id = :user_id')
            ->andWhere('a.article_id = :article_id')
            ->setParameter('user_id', $userId)
            ->setParameter('article_id', $articleid);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function deleteLikeFromArticle($userId, $articleid)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->delete()
            ->Where('a.user_id = :user_id')
            ->andWhere('a.article_id = :article_id')
            ->setParameter('user_id', $userId)
            ->setParameter('article_id', $articleid);

        return $qb->getQuery()->getResult();
    }
}
