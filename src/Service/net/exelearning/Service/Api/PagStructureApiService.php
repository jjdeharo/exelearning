<?php

namespace App\Service\net\exelearning\Service\Api;

use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use Doctrine\ORM\EntityManagerInterface;

class PagStructureApiService implements PagStructureApiServiceInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Reorders the OdePagStructureSync and its siblings.
     *
     * @param OdePagStructureSync $odePagStructureSync
     * @param int                 $newOrder
     *
     * @return OdePagStructureSync[]
     */
    public function reorderOdePagStructureSync($odePagStructureSync, $newOrder)
    {
        $modifiedOdePagStructureSyncs = [];
        $auxNewOrder = [];

        $previousOrder = $odePagStructureSync->getOdePagStructureSyncOrder();

        $auxNewOrder[$odePagStructureSync->getId()] = $newOrder;

        // Process siblings
        foreach ($odePagStructureSync->getOdeNavStructureSync()->getOdePagStructureSyncs() as $odePagStructureSyncSibling) {
            if ($odePagStructureSyncSibling->getId() != $odePagStructureSync->getId()) {
                if ($odePagStructureSyncSibling->getOdePagStructureSyncOrder() == $newOrder) {
                    if ($newOrder > $previousOrder) {
                        $auxNewOrder[$odePagStructureSyncSibling->getId()] = $odePagStructureSyncSibling->getOdePagStructureSyncOrder() - 1;
                    } else {
                        $auxNewOrder[$odePagStructureSyncSibling->getId()] = $odePagStructureSyncSibling->getOdePagStructureSyncOrder() + 1;
                    }
                } elseif ($odePagStructureSyncSibling->getOdePagStructureSyncOrder() > $newOrder) {
                    $auxNewOrder[$odePagStructureSyncSibling->getId()] = $odePagStructureSyncSibling->getOdePagStructureSyncOrder() + 1;
                } else {
                    $auxNewOrder[$odePagStructureSyncSibling->getId()] = $odePagStructureSyncSibling->getOdePagStructureSyncOrder() - 1;
                }
            }
        }

        asort($auxNewOrder, SORT_NUMERIC);

        // Process siblings
        foreach ($odePagStructureSync->getOdeNavStructureSync()->getOdePagStructureSyncs() as $odePagStructureSyncSibling) {
            $order = array_search($odePagStructureSyncSibling->getId(), array_keys($auxNewOrder));

            if (false !== $order) {
                $odePagStructureSyncSibling->setOdePagStructureSyncOrder($order + 1);
                $this->entityManager->persist($odePagStructureSyncSibling);

                $modifiedOdePagStructureSyncs[$odePagStructureSyncSibling->getId()] = $odePagStructureSyncSibling;
            }
        }

        // Force to process current OdePagStructureSync if it wasn't processed
        if (!isset($modifiedOdePagStructureSyncs[$odePagStructureSync->getId()])) {
            $order = array_search($odePagStructureSync->getId(), array_keys($auxNewOrder));

            if (false !== $order) {
                $odePagStructureSync->setOdePagStructureSyncOrder($order + 1);
                $this->entityManager->persist($odePagStructureSync);

                $modifiedOdePagStructureSyncs[$odePagStructureSync->getId()] = $odePagStructureSync;
            }
        }

        return $modifiedOdePagStructureSyncs;
    }
}
