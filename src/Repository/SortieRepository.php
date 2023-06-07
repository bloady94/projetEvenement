<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function trouverVille(){

        $qb = $this->createQueryBuilder('v');
        $qb->addOrderBy('v.nom' , 'ASC');
        $qb->leftJoin('v.ville', 'ville');
        $qb->addSelect('ville');

        return $qb->getQuery();

    }

    public function searchSorties($searchQuery, $campus, $organisateur, $inscrit, $nonInscrit, $passees, $participantId/*, $dateDebut, $dateFin*/)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Sortie::class, 's')
            ->leftJoin('s.organisateur', 'o')
            ->leftJoin('s.participants', 'i');

        if ($searchQuery) {
            $queryBuilder->where('s.nom LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        if ($campus) {
            $queryBuilder->where('s.campus = :campus')
                ->setParameter('campus', $campus);
        }

        if ($organisateur) {
            $queryBuilder->orWhere('o.id = :organisateurId')
                ->setParameter('organisateurId', $participantId);
        }


        if ($inscrit) {
            $queryBuilder->orWhere('i.id = :participantId')
                ->setParameter('participantId', $participantId);
        }
        if ($nonInscrit) {
            $queryBuilder->orWhere(':participantId NOT MEMBER OF s.participants')
                ->setParameter('participantId', $participantId);
        }


        if ($passees) {
            $queryBuilder->orWhere('s.dateHeureDebut <  :maintenant')
                ->setParameter('maintenant', new \DateTime());
        }

        /*if ($dateDebut) {
            $queryBuilder->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }

        if ($dateFin) {
            $queryBuilder->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $dateFin);
        }*/
        $query = $queryBuilder->getQuery();
        $sorties = $query->getResult();

        return $sorties;
    }





//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
