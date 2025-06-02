<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\OdePagStructureSyncProperties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdePagStructureSyncProperties|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdePagStructureSyncProperties|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdePagStructureSyncProperties[]    findAll()
 * @method OdePagStructureSyncProperties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdePagStructureSyncPropertiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdePagStructureSyncProperties::class);
    }

    // /**
    //  * @return OdePagStructureSyncProperties[] Returns an array of OdePagStructureSyncProperties objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OdePagStructureSyncProperties
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
