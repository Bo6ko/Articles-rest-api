<?php

namespace App\Repository;

use App\Entity\ArticleComments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArticleComments|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleComments|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleComments[]    findAll()
 * @method ArticleComments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleCommentsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArticleComments::class);
    }

    // /**
    //  * @return ArticleComments[] Returns an array of ArticleComments objects
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
    public function findOneBySomeField($value): ?ArticleComments
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByArticle($articleId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select('u.username, c.comment, c.id, c.article_id, c.user_id')
            ->innerJoin('App:User', 'u', 'WITH', 'c.user_id = u.id')
            ->Where('c.article_id = :article_id')
            ->setParameter('article_id', $articleId);

        return $qb->getQuery()->getArrayResult();
    }
}
