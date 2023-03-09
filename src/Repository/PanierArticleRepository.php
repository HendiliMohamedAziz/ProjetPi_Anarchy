<?php

namespace App\Repository;

use App\Entity\PanierArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PanierArticle>
 *
 * @method PanierArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method PanierArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method PanierArticle[]    findAll()
 * @method PanierArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PanierArticle::class);
    }

    public function save(PanierArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PanierArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PanierArticle[] Returns an array of PanierArticle objects
//     */
public function findByExampleField1($value): array
{
     return $this->createQueryBuilder('c')
        ->andWhere('c.idArticle = :val')
        ->setParameter('val', $value)
        ->orderBy('c.id', 'ASC')
        ->setMaxResults(100)
         ->getQuery()
       ->getResult()
   ;
}

//    public function findOneBySomeField($value): ?PanierArticle
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
