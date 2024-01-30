<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurant>
 *
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function findWithMinRating(int $minRating, AvisRepository $avisRepo)
    {
        // On récupére les Ids des restaurants ayant une note mininale de 4

        $restoId = $avisRepo->findRestoIdWithMiniRating($minRating);

        return $this->createQueryBuilder('r')
            ->select('r', 'images')
            ->leftJoin('r.images', 'images')
            ->andWhere('r.id IN (:restoId)')
            ->setParameter('restoId', $restoId)
            ->getQuery()
            ->getResult();
    }

    // public function findRestaurants(User $user)
    // {
    //     return $this->createQueryBuilder('r')
    //         ->andWhere('r.idUser = :user')
    //         ->setParameter('user', $user)
    //         ->getQuery()
    //         ->getResult();
    // }

//    /**
//     * @return Restaurant[] Returns an array of Restaurant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Restaurant
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
