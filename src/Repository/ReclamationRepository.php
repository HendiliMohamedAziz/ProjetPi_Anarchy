<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function save(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Reclamation[] Returns an array of Reclamation objects
//     */
  public function findByuser($value): array
  {
        return $this->createQueryBuilder('r')
           ->andWhere('r.user = :ahmed')
           ->setParameter('ahmed', $value)
         
       ->getQuery()
       ->getResult()
   ;
    }

    public function findByreclamation($search)
    {
        return $this->createQueryBuilder('rv')
                  //  ->innerJoin(User::class, 'coach', 'WITH', 'rv.coach = coach.id')
                    //->innerJoin(Club::class, 'club', 'WITH', 'rv.club = club.id')
                    //->innerJoin(Article::class, 'article', 'WITH', 'rv.article = article.id')
                    ->andWhere('rv.message LIKE :nom  ')
                //    ->orWhere('club.nom LIKE :nom ')
                  //  ->orWhere('article.nom LIKE :nom ')
                    ->setParameter('nom','%'.$search.'%')
                    ->getQuery()
                    ->getResult();
    }

    public function getReclamationsStatus()
{
    $qb = $this->createQueryBuilder('r')
        ->select('r.etat, COUNT(r.id) as count')
        ->groupBy('r.etat');

    return $qb->getQuery()->getResult();
}


   
    public function findbynontraite($value): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.etat=:val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
}
