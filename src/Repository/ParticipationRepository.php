<?php

namespace App\Repository;

use App\Entity\Club;
use App\Entity\User;
use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participation>
 *
 * @method Participation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participation[]    findAll()
 * @method Participation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    public function add(Participation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Participation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Participation[] Returns an array of Participation objects
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

    //    public function findOneBySomeField($value): ?Participation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }



    /*public function showParticipationClub(){
        return $this->createQueryBuilder('p')
            ->select('p', 'c.nom')
            ->leftJoin('p.club', 'c')
            ->getQuery()
            ->getResult();
    }*/
    public function findMesClubs($userId)
    {
        $query = $this->createQueryBuilder('participation')
            ->select('club.nom', 'club.image', 'club.Description', 'club.type_activite', 'participation.DateDebut', 'participation.DateFin')
            ->join('participation.id_club', 'club')
            ->where('participation.id_user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        return $query->getResult();
    }


    public function findByIdClient1($clientId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->leftJoin('p.id_club', 'c')
            ->andWhere('c.id_clubOwner = :clientId')
            ->setParameter('clientId', $clientId);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
    /*
    public function findByClubOwnerAndClient($clubOwnerId, $clientId)
{
    return $this->createQueryBuilder('p')
        ->innerJoin('p.id_club', 'c')
        ->innerJoin('c.id_clubOwner', 'cu')
        ->innerJoin('p.id_user', 'cl')
        ->where('cu.roles LIKE :roles')
        ->andWhere('cu.id = :clubOwnerId')
        ->andWhere('cl.id = :clientId')
        ->setParameter('roles', '%"ROLE_CLUBOWNER"%')
        ->setParameter('clubOwnerId', $clubOwnerId)
        ->setParameter('clientId', $clientId)
        ->getQuery()
        ->getResult();
}*/ /*
    public function findParticipationsByClubOwner($clubOwnerId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.id_club', 'c')
            ->join('c.id_clubOwner', 'co')
            ->join('p.id_user', 'u')
            ->where('co = :clubOwner')
            ->setParameter('clubOwner', $clubOwnerId)
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_CLIENT"%');

        return $qb->getQuery()->getResult();
    }*/

    public function findParticipationsByClubOwner($clubOwnerId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p', 'c', 'u')
            ->join('p.id_club', 'c')
            ->join('c.id_clubOwner', 'co')
            ->join('p.id_user', 'u')
            ->where('co = :clubOwner')
            ->setParameter('clubOwner', $clubOwnerId)
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_CLIENT"%');

        return $qb->getQuery()->getResult();
    }


    /*
public function findClientsByClubOwner($clubOwnerId)
{
    return $this->createQueryBuilder('c')
        ->select('c.nom AS club_name, cl.nom AS client_name')
        ->leftJoin('c.clients', 'cl')
        ->where('c.clubOwner = :clubOwnerId')
        ->setParameter('clubOwnerId', $clubOwnerId)
        ->getQuery()
        ->getResult();
}*/
    ///////////////////////////////////////////////mayekhdmouch
    /*
public function findClientsByClubOwner($clubOwnerId)
{
    $qb = $this->createQueryBuilder('c');
    
    $qb->select('u')
       ->from('App\Entity\User', 'u')
       ->join('c.id_user', 'p')
       ->where($qb->expr()->eq('c.id_club', ':clubOwnerId'))
       ->andWhere($qb->expr()->eq('u.roles', ':role'))
       ->setParameter('clubOwnerId', $clubOwnerId)
       ->setParameter('role', 'ROLE_CLIENT');

    return $qb->getQuery()->getResult();
}

public function findClientsParticipations($clubOwnerId) {
    $qb = $this->createQueryBuilder('p')
        ->select('u.nom')
        ->innerJoin('p.id_club', 'c')
        ->innerJoin('p.id_user', 'u')
        ->innerJoin('c.id_clubOwner', 'o')
        ->where('o.id = :clubOwnerId')
        ->andWhere('JSON_CONTAINS(u.roles, :["ROLE_CLIENT"]) = 1')
        ->setParameter('clubOwnerId', $clubOwnerId)
        ->setParameter('["ROLE_CLIENT"]', '{"client"}');

    return $qb->getQuery()->getResult();
}*/
    /*public function findByIdClient($clientId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.id_user = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('p.DateDebut', 'DESC');
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }*/

    public function findByIdClient($clientId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.id_user = :clientId')
            ->andWhere('p.participated = :participated')
            ->setParameter('clientId', $clientId)
            ->setParameter('participated', true)
            ->orderBy('p.DateDebut', 'DESC');
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    /*
public function orderByDate(){

    $em = $this->getEntityManager();
    $query = $em->createQuery('select p from App\Entity\Participation p order by p.DateDebut ASC');
    return $query->getResult();

}
*/
    public function allParticipations()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();
    }


    public function findActiveParticipation(User $user, Club $club): ?Participation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id_user = :user')
            ->andWhere('p.id_club = :club')
            ->andWhere('p.participated = :participated')
            ->setParameter('user', $user)
            ->setParameter('club', $club)
            ->setParameter('participated', true)

            ->getQuery()
            ->getOneOrNullResult();
    }
}
