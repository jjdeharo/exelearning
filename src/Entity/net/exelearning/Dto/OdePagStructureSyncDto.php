<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\OdePagStructureSync;

/**
 * OdePagStructureSyncDto.
 */
class OdePagStructureSyncDto extends BaseDto
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $odeNavStructureSyncId;

    /**
     * @var string
     */
    protected $odeSessionId;

    /**
     * @var string
     */
    protected $pageId;

    /**
     * @var string
     */
    protected $blockId;

    /**
     * @var string
     */
    protected $blockName;

    /**
     * @var string
     */
    protected $iconName;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @var int
     */
    protected $order;

    /**
     * @var OdeComponentsSyncDto[]
     */
    protected $odeComponentsSyncs;

    /**
     * @var OdePagStructureSyncPropertiesDto[]
     */
    protected $odePagStructureSyncProperties;

    public function __construct()
    {
        $this->odeComponentsSyncs = [];
        $this->odePagStructureSyncProperties = [];
    }

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
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param string $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * @return string
     */
    public function getBlockId()
    {
        return $this->blockId;
    }

    /**
     * @param string $blockId
     */
    public function setBlockId($blockId)
    {
        $this->blockId = $blockId;
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return $this->blockName;
    }

    /**
     * @param string $blockName
     */
    public function setBlockName($blockName)
    {
        $this->blockName = $blockName;
    }

    /**
     * @return string
     */
    public function getIconName()
    {
        return $this->iconName;
    }

    /**
     * @param string $iconName
     */
    public function setIconName($iconName)
    {
        $this->iconName = $iconName;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return number
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param number $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncDto
     */
    public function getOdeComponentsSyncs()
    {
        return $this->odeComponentsSyncs;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncDto $odeComponentsSyncs
     */
    public function setOdeComponentsSyncs($odeComponentsSyncs)
    {
        $this->odeComponentsSyncs = $odeComponentsSyncs;
    }

    /**
     * @param OdeComponentsSyncDto $odeComponentsSync
     */
    public function addOdeComponentsSyncs($odeComponentsSync)
    {
        $this->odeComponentsSyncs[] = $odeComponentsSync;
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\OdePagStructureSyncPropertiesDto
     */
    public function getOdePagStructureSyncProperties()
    {
        return $this->odePagStructureSyncProperties;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdePagStructureSyncPropertiesDto $odePagStructureSyncProperties
     */
    public function setOdePagStructureSyncProperties($odePagStructureSyncProperties)
    {
        $this->odePagStructureSyncProperties = $odePagStructureSyncProperties;
    }

    /**
     * @param OdePagStructureSyncPropertiesDto $odePagStructureSyncProperties
     */
    public function addOdePagStructureSyncProperties($odePagStructureSyncProperties)
    {
        $this->odePagStructureSyncProperties[] = $odePagStructureSyncProperties;
    }

    /**
     * Loads OdePagStructureSyncDto from OdePagStructureSync object.
     *
     * @param OdePagStructureSync $odePagStructureSync
     * @param bool                $loadOdeComponentsSync
     * @param bool                $loadOdePagStructureSyncProperties
     * @param bool                $loadOdeComponentsSyncProperties
     */
    public function loadFromEntity($odePagStructureSync, $loadOdeComponentsSync, $loadOdePagStructureSyncProperties, $loadOdeComponentsSyncProperties)
    {
        $this->setId($odePagStructureSync->getId());
        $this->setOdeNavStructureSyncId($odePagStructureSync->getOdeNavStructureSync()->getId());
        $this->setOdeSessionId($odePagStructureSync->getOdeSessionId());
        $this->setPageId($odePagStructureSync->getOdePageId());
        $this->setBlockId($odePagStructureSync->getOdeBlockId());
        $this->setBlockName($odePagStructureSync->getBlockName());

        if (!empty($odePagStructureSync->getIconName())) {
            $this->setIconName($odePagStructureSync->getIconName());
        }

        if (!empty($odePagStructureSync->getOdePagStructureSync())) {
            $this->setParent($odePagStructureSync->getOdePagStructureSync()->getId());
        }
        $this->setOrder($odePagStructureSync->getOdePagStructureSyncOrder());

        if ($loadOdeComponentsSync) {
            foreach ($odePagStructureSync->getOdeComponentsSyncs() as $odeComponentsSync) {
                $odeComponentsSyncDto = new OdeComponentsSyncDto();
                $odeComponentsSyncDto->loadFromEntity($odeComponentsSync, $loadOdeComponentsSyncProperties);

                $this->addOdeComponentsSyncs($odeComponentsSyncDto);
            }
        } else {
            $this->setOdeComponentsSyncs(null);
        }

        // Load odePagStructureSyncProperties from config and database
        if ($loadOdePagStructureSyncProperties) {
            $odePagStructureSync->loadOdePagStructureSyncPropertiesFromConfig(); // initialize default values

            $odePagStructureSyncPropertiesDtos = [];

            foreach ($odePagStructureSync->getOdePagStructureSyncProperties() as $odePagStructureSyncProperties) {
                $odePagStructureSyncPropertiesDto = new OdePagStructureSyncPropertiesDto();
                $odePagStructureSyncPropertiesDto->loadFromEntity($odePagStructureSyncProperties);

                $odePagStructureSyncPropertiesDtos[$odePagStructureSyncProperties->getKey()] = $odePagStructureSyncPropertiesDto;
            }

            $this->setOdePagStructureSyncProperties($odePagStructureSyncPropertiesDtos);
        }
    }
}
