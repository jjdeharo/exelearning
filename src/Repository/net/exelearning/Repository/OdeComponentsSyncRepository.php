<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdeComponentsSync|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdeComponentsSync|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdeComponentsSync[]    findAll()
 * @method OdeComponentsSync[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdeComponentsSyncRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdeComponentsSync::class);
    }

    // /**
    //  * @return OdeComponentsSync[] Returns an array of OdeComponentsSync objects
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
    public function findOneBySomeField($value): ?OdeComponentsSync
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
