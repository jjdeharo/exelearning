<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\OdeNavStructureSyncProperties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdeNavStructureSyncProperties|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdeNavStructureSyncProperties|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdeNavStructureSyncProperties[]    findAll()
 * @method OdeNavStructureSyncProperties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdeNavStructureSyncPropertiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdeNavStructureSyncProperties::class);
    }

    // /**
    //  * @return OdeNavStructureSyncProperties[] Returns an array of OdeNavStructureSyncProperties objects
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
    public function findOneBySomeField($value): ?OdeNavStructureSyncProperties
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
