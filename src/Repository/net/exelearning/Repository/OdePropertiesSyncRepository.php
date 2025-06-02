<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OdePropertiesSync|null find($id, $lockMode = null, $lockVersion = null)
 * @method OdePropertiesSync|null findOneBy(array $criteria, array $orderBy = null)
 * @method OdePropertiesSync[]    findAll()
 * @method OdePropertiesSync[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OdePropertiesSyncRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OdePropertiesSync::class);
    }

    // /**
    //  * @return OdeProperties[] Returns an array of OdeProperties objects
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
    public function findOneBySomeField($value): ?OdeProperties
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Removes odePropertiesSyncs by odeSessionId.
     *
     * @param string $odeSessionId
     *
     * @return number
     */
    public function removeByOdeSessionId($odeSessionId)
    {
        $queryDeleteOdeProperties = $this->createQueryBuilder('p')
        ->delete()
        ->where('p.odeSessionId = :odeSessionId')
        ->setParameter('odeSessionId', $odeSessionId)
        ->getQuery();

        $odePropertiesDeleted = $queryDeleteOdeProperties->execute();

        return $odePropertiesDeleted;
    }

    /**
     * Updates odePropertiesSyncs by odeSessionId.
     *
     * @param string $odeSessionId
     * @param string $newOdeSessionId
     *
     * @return number
     */
    public function updateByOdeSessionId($odeSessionId, $newOdeSessionId)
    {
        $queryUpdateOdeProperties = $this->createQueryBuilder('p')
        ->update()
        ->set('p.odeSessionId', ':newOdeSessionId')
        ->setParameter('newOdeSessionId', $newOdeSessionId)
        ->where('p.odeSessionId = :odeSessionId')
        ->setParameter('odeSessionId', $odeSessionId)
        ->getQuery();

        $odePropertiesUpdated = $queryUpdateOdeProperties->execute();

        return $odePropertiesUpdated;
    }
}
