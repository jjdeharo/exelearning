<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\CurrentOdeUsers;

/**
 * CurrentOdeUsersDto.
 */
class CurrentOdeUsersDto extends BaseDto
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $odeId;

    /**
     * @var string
     */
    protected $odeVersionId;

    /**
     * @var string
     */
    protected $odeSessionId;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $lastAction;

    /**
     * @var string
     */
    protected $currentPageId;

    /**
     * @var string
     */
    protected $currentBlockId;

    /**
     * @var string
     */
    protected $currentComponentId;

    /**
     * @var string
     */
    protected $odePageIdUpdate;

    /**
     * @var string
     */
    protected $odeBlockIdUpdate;

    /**
     * @var string
     */
    protected $odeComponentIdUpdate;

    /**
     * @var string
     */
    protected $destinationPageIdUpdate;

    /**
     * @var string
     */
    protected $actionTypeUpdate;

    /**
     * @var string
     */
    protected $lastSync;

    /**
     * @var string
     */
    protected $syncSaveFlag;

    /**
     * @var string
     */
    protected $syncNavStructureFlag;

    /**
     * @var string
     */
    protected $syncPagStructureFlag;

    /**
     * @var string
     */
    protected $syncComponentsFlag;

    /**
     * @var string
     */
    protected $syncUpdateFlag;

    /**
     * @var string
     */
    protected $nodeIp;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOdeId()
    {
        return $this->odeId;
    }

    /**
     * @param string $odeId
     */
    public function setOdeId($odeId)
    {
        $this->odeId = $odeId;
    }

    /**
     * @return string
     */
    public function getOdeVersionId()
    {
        return $this->odeVersionId;
    }

    /**
     * @param string $odeVersionId
     */
    public function setOdeVersionId($odeVersionId)
    {
        $this->odeVersionId = $odeVersionId;
    }

    /**
     * @return string
     */
    public function getOdeSessionId()
    {
        return $this->odeSessionId;
    }

    /**
     * @param string $odeSessionId
     */
    public function setOdeSessionId($odeSessionId)
    {
        $this->odeSessionId = $odeSessionId;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getLastAction()
    {
        return $this->lastAction;
    }

    /**
     * @param string $lastAction
     */
    public function setLastAction($lastAction)
    {
        $this->lastAction = $lastAction;
    }

    /**
     * @return string
     */
    public function getCurrentPageId()
    {
        return $this->currentPageId;
    }

    /**
     * @param string $currentPageId
     */
    public function setCurrentPageId($currentPageId)
    {
        $this->currentPageId = $currentPageId;
    }

    /**
     * @return string
     */
    public function getCurrentBlockId()
    {
        return $this->currentBlockId;
    }

    /**
     * @param string $currentBlockId
     */
    public function setCurrentBlockId($currentBlockId)
    {
        $this->currentBlockId = $currentBlockId;
    }

    /**
     * @return string
     */
    public function getCurrentComponentId()
    {
        return $this->currentComponentId;
    }

    /**
     * @param string $currentComponentId
     */
    public function setCurrentComponentId($currentComponentId)
    {
        $this->currentComponentId = $currentComponentId;
    }

    /**
     * @return string
     */
    public function getOdePageIdUpdate()
    {
        return $this->odePageIdUpdate;
    }

    /**
     * @param string $odePageIdUpdate
     */
    public function setOdePageIdUpdate($odePageIdUpdate)
    {
        $this->odePageIdUpdate = $odePageIdUpdate;
    }

    /**
     * @return string
     */
    public function getOdeBlockIdUpdate()
    {
        return $this->odeBlockIdUpdate;
    }

    /**
     * @param string $odeBlockIdUpdate
     */
    public function setOdeBlockIdUpdate($odeBlockIdUpdate)
    {
        $this->odeBlockIdUpdate = $odeBlockIdUpdate;
    }

    /**
     * @return string
     */
    public function getOdeComponentIdUpdate()
    {
        return $this->odeComponentIdUpdate;
    }

    /**
     * @param string $odeComponentIdUpdate
     */
    public function setOdeComponentIdUpdate($odeComponentIdUpdate)
    {
        $this->odeComponentIdUpdate = $odeComponentIdUpdate;
    }

    /**
     * @return string
     */
    public function getDestinationPageIdUpdate()
    {
        return $this->destinationPageIdUpdate;
    }

    /**
     * @param string $destinationPageIdUpdate
     */
    public function setDestinationPageIdUpdate($destinationPageIdUpdate)
    {
        $this->destinationPageIdUpdate = $destinationPageIdUpdate;
    }

    /**
     * @return string
     */
    public function getActionTypeUpdate()
    {
        return $this->actionTypeUpdate;
    }

    /**
     * @param string $actionTypeUpdate
     */
    public function setActionTypeUpdate($actionTypeUpdate)
    {
        $this->actionTypeUpdate = $actionTypeUpdate;
    }

    /**
     * @return string
     */
    public function getLastSync()
    {
        return $this->lastSync;
    }

    /**
     * @param string $lastSync
     */
    public function setLastSync($lastSync)
    {
        $this->lastSync = $lastSync;
    }

    /**
     * @return string
     */
    public function getSyncSaveFlag()
    {
        return $this->syncSaveFlag;
    }

    /**
     * @param string $syncSaveFlag
     */
    public function setSyncSaveFlag($syncSaveFlag)
    {
        $this->syncSaveFlag = $syncSaveFlag;
    }

    /**
     * @return string
     */
    public function getSyncNavStructureFlag()
    {
        return $this->syncNavStructureFlag;
    }

    /**
     * @param string $syncNavStructureFlag
     */
    public function setSyncNavStructureFlag($syncNavStructureFlag)
    {
        $this->syncNavStructureFlag = $syncNavStructureFlag;
    }

    /**
     * @return string
     */
    public function getSyncPagStructureFlag()
    {
        return $this->syncPagStructureFlag;
    }

    /**
     * @param string $syncPagStructureFlag
     */
    public function setSyncPagStructureFlag($syncPagStructureFlag)
    {
        $this->syncPagStructureFlag = $syncPagStructureFlag;
    }

    /**
     * @return string
     */
    public function getSyncComponentsFlag()
    {
        return $this->syncComponentsFlag;
    }

    /**
     * @param string $syncComponentsFlag
     */
    public function setSyncComponentsFlag($syncComponentsFlag)
    {
        $this->syncComponentsFlag = $syncComponentsFlag;
    }

    /**
     * @return string
     */
    public function getSyncUpdateFlag()
    {
        return $this->syncUpdateFlag;
    }

    /**
     * @param string $syncUpdateFlag
     */
    public function setSyncUpdateFlag($syncUpdateFlag)
    {
        $this->syncUpdateFlag = $syncUpdateFlag;
    }

    /**
     * @return string
     */
    public function getNodeIp()
    {
        return $this->nodeIp;
    }

    /**
     * @param string $nodeIp
     */
    public function setNodeIp($nodeIp)
    {
        $this->nodeIp = $nodeIp;
    }

    /**
     * Loads CurrentOdeUsersDto from currentOdeUsers object.
     *
     * @param CurrentOdeUsers $currentOdeUsers
     */
    public function loadFromEntity($currentOdeUsers)
    {
        $this->setId($currentOdeUsers->getId());
        $this->setOdeId($currentOdeUsers->getOdeId());
        $this->setOdeVersionId($currentOdeUsers->getOdeVersionId());
        $this->setOdeSessionId($currentOdeUsers->getOdeSessionId());
        $this->setUser($currentOdeUsers->getUser());

        if (!empty($currentOdeUsers->getLastAction())) {
            $this->setLastAction($currentOdeUsers->getLastAction()->getTimestamp());
        }

        $this->setCurrentPageId($currentOdeUsers->getCurrentPageId());
        $this->setCurrentBlockId($currentOdeUsers->getCurrentBlockId());
        $this->setCurrentComponentId($currentOdeUsers->getCurrentComponentId());

        if (!empty($currentOdeUsers->getLastSync())) {
            $this->setLastSync($currentOdeUsers->getLastSync()->getTimestamp());
        }

        $this->setSyncSaveFlag($currentOdeUsers->getSyncSaveFlag());
        $this->setSyncNavStructureFlag($currentOdeUsers->getSyncNavStructureFlag());
        $this->setSyncPagStructureFlag($currentOdeUsers->getSyncPagStructureFlag());
        $this->setSyncComponentsFlag($currentOdeUsers->getSyncComponentsFlag());
        $this->setNodeIp($currentOdeUsers->getNodeIp());
    }
}
