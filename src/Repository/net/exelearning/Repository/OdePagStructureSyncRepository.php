<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdePagStructureSync|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdePagStructureSync|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdePagStructureSync[]    findAll()
 * @method OdePagStructureSync[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdePagStructureSyncRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdePagStructureSync::class);
    }

    // /**
    //  * @return OdePagStructureSync[] Returns an array of OdePagStructureSync objects
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
    public function findOneBySomeField($value): ?OdePagStructureSync
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
