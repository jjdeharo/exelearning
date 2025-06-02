<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdeNavStructureSyncDataSaveDto.
 */
class OdeNavStructureSyncDataSaveDto extends BaseDto
{
    /**
     * @var string
     */
    protected $responseMessage;

    /**
     * @var string
     */
    protected $odeNavStructureSyncId;

    /**
     * @var OdeNavStructureSyncDto
     */
    protected $odeNavStructureSync;

    /**
     * @var bool
     */
    protected $isNewOdeNavStructureSync;

    /**
     * @var OdeNavStructureSyncDto[]
     */
    protected $odeNavStructureSyncs;

    public function __construct()
    {
        $this->odeNavStructureSyncs = [];
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

    /**
     * @return bool
     */
    public function isNewOdeNavStructureSync()
    {
        return $this->isNewOdeNavStructureSync;
    }

    /**
     * @param bool $isNewOdeNavStructureSync
     */
    public function setIsNewOdeNavStructureSync($isNewOdeNavStructureSync)
    {
        $this->isNewOdeNavStructureSync = $isNewOdeNavStructureSync;
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\OdeNavStructureDto
     */
    public function getOdeNavStructureSyncs()
    {
        return $this->odeNavStructureSyncs;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdeNavStructureDto $odeNavStructureSyncs
     */
    public function setOdeNavStructureSyncs($odeNavStructureSyncs)
    {
        $this->odeNavStructureSyncs = $odeNavStructureSyncs;
    }

    /**
     * @param OdeNavStructureSyncDto $odeNavStructureSync
     */
    public function addOdeNavStructureSyncs($odeNavStructureSync)
    {
        $this->odeNavStructureSyncs[] = $odeNavStructureSync;
    }
}
