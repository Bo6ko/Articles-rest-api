<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
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
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findArticleByUsername($username, $current_page, $num_result)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select('u.username, u.isActive, a.title, a.body, a.id')
            ->innerJoin('App:User', 'u', 'WITH', 'a.user = u.id')
            ->Where('u.username = :username')
            ->setParameter('username', $username)
            ->setFirstResult($current_page * $num_result)
            ->setMaxResults($num_result);

        return $qb->getQuery()->getArrayResult();
    }

    public function findByArticlesCountFromCurrentUser($username) {
        $queryBuilder = $this->createQueryBuilder('a');
        return $queryBuilder
            ->select($queryBuilder->expr()->count('a'))
            ->innerJoin('App:User', 'u', 'WITH', 'a.user = u.id')
            ->Where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()->getSingleScalarResult();
    }

    public function  findByArticlePaginated($current_page, $num_result) {
        $current_page--;
        $qb = $this->createQueryBuilder('a');
        $qb->select('a')
            ->setFirstResult($current_page * $num_result)
            ->setMaxResults($num_result);
        return $qb->getQuery()->getResult();
    }

    public function findByArticlesCount() {
        $queryBuilder = $this->createQueryBuilder('a');
        return $queryBuilder
            ->select($queryBuilder->expr()->count('a'))
            ->getQuery()->getSingleScalarResult();
    }

}
