<?php

namespace App\Repository;

use App\Entity\Avis;
use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 *
 * @method Avis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avis[]    findAll()
 * @method Avis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function findRestoIdWithMiniRating(int $miniRating)
    {
        return $this->createQueryBuilder('a')
            ->select('IDENTITY(a.idResto) as idResto')
            ->andWhere('a.note >= :miniRating')  // Utilisation correcte du nom du paramètre
            ->setParameter('miniRating', $miniRating)  // Déclaration du paramètre correcte
            ->groupBy('a.idResto')
            ->getQuery()
            ->getResult();
    }

    public function getMoyenneNotesByResto(Restaurant $resto): ?float
    {
        return $this->createQueryBuilder('a')
            ->select('AVG(a.note) as moyenne')
            ->andWhere('a.idResto = :resto')
            ->setParameter('resto', $resto)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Avis[] Returns an array of Avis objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Avis
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
