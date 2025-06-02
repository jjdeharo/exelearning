<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\OdeOperationsLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdeOperationsLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdeOperationsLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdeOperationsLog[]    findAll()
 * @method OdeOperationsLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdeOperationsLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdeOperationsLog::class);
    }

    /**
     * getLastOdeOperationLog.
     *
     * @return OdeOperationLog
     */
    public function getLastOdeOperationLog()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.updatedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
