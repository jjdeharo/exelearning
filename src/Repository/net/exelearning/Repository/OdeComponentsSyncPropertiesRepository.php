<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\OdeComponentsSyncProperties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdeComponentsSyncProperties|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdeComponentsSyncProperties|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdeComponentsSyncProperties[]    findAll()
 * @method OdeComponentsSyncProperties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdeComponentsSyncPropertiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdeComponentsSyncProperties::class);
    }

    // /**
    //  * @return OdeComponentsSyncProperties[] Returns an array of OdeComponentsSyncProperties objects
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
    public function findOneBySomeField($value): ?OdeComponentsSyncProperties
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
