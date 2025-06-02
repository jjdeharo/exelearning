<?php

namespace App\Service\net\exelearning\Service\Api;

use App\Entity\net\exelearning\Entity\OdePagStructureSync;

interface PagStructureApiServiceInterface
{
    /**
     * Reorders the OdePagStructureSync and its siblings.
     *
     * @param OdePagStructureSync $odePagStructureSync
     * @param int                 $newOrder
     *
     * @return OdePagStructureSync[]
     */
    public function reorderOdePagStructureSync($odePagStructureSync, $newOrder);
}
