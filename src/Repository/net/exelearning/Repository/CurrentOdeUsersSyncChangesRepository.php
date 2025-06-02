<?php

namespace App\Repository\net\exelearning\Repository;

use App\Entity\net\exelearning\Entity\CurrentOdeUsersSyncChanges;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CurrentOdeUsersSyncChanges|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrentOdeUsersSyncChanges|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrentOdeUsersSyncChanges[]    findAll()
 * @method CurrentOdeUsersSyncChanges[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrentOdeUsersSyncChangesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrentOdeUsersSyncChanges::class);
    }

    // /**
    //  * @return CurrentOdeUsers[] Returns an array of CurrentOdeUsers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CurrentOdeUsers
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Remove sync changes from the BBDD by user.
     *
     * @param string $user
     *
     * @return DeletedSyncChyanges
     */
    public function removeCurrentOdeUsersSyncChangesByUser($user)
    {
        $queryDeleteCurrentOdeUsersSyncChanges = $this->createQueryBuilder('n')
            ->delete()
            ->where('n.userUpdate = :user')
            ->setParameter('user', $user)
            ->getQuery();

        $syncTablesChangesDeleted = $queryDeleteCurrentOdeUsersSyncChanges->execute();

        return $syncTablesChangesDeleted;
    }

    /**
     * Get list of sync changes from the BBDD by user.
     *
     * @param string $user
     *
     * @return SyncChanges
     */
    public function getSyncChangesListByUser($user)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.userUpdate = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'ASC');

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}
