<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdePagStructureSyncDataSaveDto.
 */
class OdePagStructureSyncDataSaveDto extends BaseDto
{
    /**
     * @var string
     */
    protected $responseMessage;

    /**
     * @var string
     */
    protected $odePagStructureSyncId;

    /**
     * @var OdePagStructureSyncDto
     */
    protected $odePagStructureSync;

    /**
     * @var bool
     */
    protected $isNewOdePagStructureSync;

    /**
     * @var OdePagStructureSyncDto[]
     */
    protected $odePagStructureSyncs;

    /**
     * @var string
     */
    protected $odeNavStructureSyncId;

    /**
     * @var OdeNavStructureSyncDto
     */
    protected $odeNavStructureSync;

    public function __construct()
    {
        $this->odePagStructureSyncs = [];
    }

    /**
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    /**
     * @param string $responseMessage
     */
    public function setResponseMessage($responseMessage)
    {
        $this->responseMessage = $responseMessage;
    }

    /**
     * @return string
     */
    public function getOdePagStructureSyncId()
    {
        return $this->odePagStructureSyncId;
    }

    /**
     * @param string $odePagStructureSyncId
     */
    public function setOdePagStructureSyncId($odePagStructureSyncId)
    {
        $this->odePagStructureSyncId = $odePagStructureSyncId;
    }

    /**
     * @return OdePagStructureSyncDto
     */
    public function getOdePagStructureSync()
    {
        return $this->odePagStructureSync;
    }

    /**
     * @param OdePagStructureSyncDto $odePagStructureSync
     */
    public function setOdePagStructureSync($odePagStructureSync)
    {
        $this->odePagStructureSync = $odePagStructureSync;
    }

    /**
     * @return bool
     */
    public function isNewOdePagStructureSync()
    {
        return $this->isNewOdePagStructureSync;
    }

    /**
     * @param bool $isNewOdePagStructureSync
     */
    public function setIsNewOdePagStructureSync($isNewOdePagStructureSync)
    {
        $this->isNewOdePagStructureSync = $isNewOdePagStructureSync;
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\OdePagStructureDto
     */
    public function getOdePagStructureSyncs()
    {
        return $this->odePagStructureSyncs;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdePagStructureDto $odePagStructureSyncs
     */
    public function setOdePagStructureSyncs($odePagStructureSyncs)
    {
        $this->odePagStructureSyncs = $odePagStructureSyncs;
    }

    /**
     * @param OdePagStructureSyncDto $odePagStructureSync
     */
    public function addOdePagStructureSyncs($odePagStructureSync)
    {
        $this->odePagStructureSyncs[] = $odePagStructureSync;
    }

    /**
     * @return string
     */
    public function getOdeNavStructureSyncId()
    {
        return $this->odeNavStructureSyncId;
    }

    /**
     * @param string $odeNavStructureSyncId
     */
    public function setOdeNavStructureSyncId($odeNavStructureSyncId)
    {
        $this->odeNavStructureSyncId = $odeNavStructureSyncId;
    }

    public function getOdeNavStructureSync()
    {
        return $this->odeNavStructureSync;
    }

    public function setOdeNavStructureSync($odeNavStructureSync)
    {
        $this->odeNavStructureSync = $odeNavStructureSync;
    }
}
