<?php

namespace App\Repository\net\exelearning\Repository;

use Doctrine\DBAL\Connection;

class GameRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getIdevicesBySessionId(string $odeSessionId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(
            'p.id AS navId',
            'p.ode_nav_structure_sync_id',
            'p.ode_session_id',
            'p.ode_page_id AS odePageId',
            'p.ode_parent_page_id AS odeParentPageId',
            'p.page_name AS pageName',
            'p.ode_nav_structure_sync_order',
            'p.is_active AS navIsActive',
            'c.id AS componentId',
            'c.ode_pag_structure_sync_id',
            'c.html_view AS htmlViewer',
            'c.json_properties AS jsonProperties',
            'c.ode_session_id AS componentSessionId',
            'c.ode_page_id AS componentPageId',
            'c.ode_block_id',
            'c.ode_idevice_id',
            'c.ode_idevice_type_name AS odeIdeviceTypeName',
            'c.ode_components_sync_order',
            'c.is_active AS componentIsActive',
            'ps.block_name AS blockName'
        )
            ->from('ode_nav_structure_sync', 'p')
            ->innerJoin('p', 'ode_components_sync', 'c', 'p.ode_page_id = c.ode_page_id')
            ->innerJoin('c', 'ode_pag_structure_sync', 'ps', 'c.ode_block_id = ps.ode_block_id')
            ->where('p.ode_session_id = :odeSessionId')
            ->setParameter('odeSessionId', $odeSessionId)
            ->addOrderBy('p.ode_nav_structure_sync_order', 'ASC')
            ->addOrderBy('c.ode_pag_structure_sync_id', 'ASC');

        return $qb->executeQuery()->fetchAllAssociative();
    }
}
