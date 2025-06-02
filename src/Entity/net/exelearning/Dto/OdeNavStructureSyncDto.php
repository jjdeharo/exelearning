<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\OdeNavStructureSync;

/**
 * OdeNavStructureSyncDto.
 */
class OdeNavStructureSyncDto extends BaseDto
{
    /**
     * @var string
     */
    protected $id;

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
    protected $pageName;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @var int
     */
    protected $order;

    /**
     * @var OdePagStructureSyncDto[]
     */
    protected $odePagStructureSyncs;

    /**
     * @var OdeNavStructureSyncPropertiesDto[]
     */
    protected $odeNavStructureSyncProperties;

    public function __construct()
    {
        $this->odePagStructureSyncs = [];
        $this->odeNavStructureSyncProperties = [];
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
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     */
    public function setPageName($pageName)
    {
        $this->pageName = $pageName;
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
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
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
     * @return multitype:\App\Entity\net\exelearning\Dto\OdeNavStructureSyncPropertiesDto
     */
    public function getOdeNavStructureSyncProperties()
    {
        return $this->odeNavStructureSyncProperties;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdeNavStructureSyncPropertiesDto $odeNavStructureSyncProperties
     */
    public function setOdeNavStructureSyncProperties($odeNavStructureSyncProperties)
    {
        $this->odeNavStructureSyncProperties = $odeNavStructureSyncProperties;
    }

    /**
     * @param OdeNavStructureSyncPropertiesDto $odeNavStructureSyncProperties
     */
    public function addOdeNavStructureSyncProperties($odeNavStructureSyncProperties)
    {
        $this->odeNavStructureSyncProperties[] = $odeNavStructureSyncProperties;
    }

    /**
     * Loads OdeNavStructureSyncDto from OdeNavStructureSync object.
     *
     * @param OdeNavStructureSync $odeNavStructureSync
     * @param bool                $loadOdePagStructureSyncs
     * @param bool                $loadOdeComponentsSync
     * @param bool                $loadOdeNavStructureSyncProperties
     * @param bool                $loadOdePagStructureSyncProperties
     * @param bool                $loadOdeComponentsSyncProperties
     */
    public function loadFromEntity($odeNavStructureSync, $loadOdePagStructureSyncs, $loadOdeComponentsSync, $loadOdeNavStructureSyncProperties, $loadOdePagStructureSyncProperties, $loadOdeComponentsSyncProperties)
    {
        $this->setId($odeNavStructureSync->getId());
        $this->setOdeSessionId($odeNavStructureSync->getOdeSessionId());
        $this->setPageId($odeNavStructureSync->getOdePageId());
        $this->setPageName($odeNavStructureSync->getPageName());
        if (!empty($odeNavStructureSync->getOdeNavStructureSync())) {
            $this->setParent($odeNavStructureSync->getOdeNavStructureSync()->getId());
        }
        $this->setOrder($odeNavStructureSync->getOdeNavStructureSyncOrder());

        if ($loadOdePagStructureSyncs) {
            foreach ($odeNavStructureSync->getOdePagStructureSyncs() as $odePagStructureSync) {
                $odePagStructureSyncDto = new OdePagStructureSyncDto();
                $odePagStructureSyncDto->loadFromEntity($odePagStructureSync, $loadOdeComponentsSync, $loadOdePagStructureSyncProperties, $loadOdeComponentsSyncProperties);

                $this->addOdePagStructureSyncs($odePagStructureSyncDto);
            }
        } else {
            $this->setOdePagStructureSyncs(null);
        }

        // Load odeNavStructureSyncProperties from config and database
        if ($loadOdeNavStructureSyncProperties) {
            $odeNavStructureSync->loadOdeNavStructureSyncPropertiesFromConfig(); // initialize default values

            $odeNavStructureSyncPropertiesDtos = [];

            foreach ($odeNavStructureSync->getOdeNavStructureSyncProperties() as $odeNavStructureSyncProperties) {
                $odeNavStructureSyncPropertiesDto = new OdeNavStructureSyncPropertiesDto();
                $odeNavStructureSyncPropertiesDto->loadFromEntity($odeNavStructureSyncProperties);

                $odeNavStructureSyncPropertiesDtos[$odeNavStructureSyncProperties->getKey()] = $odeNavStructureSyncPropertiesDto;
            }

            $this->setOdeNavStructureSyncProperties($odeNavStructureSyncPropertiesDtos);
        }
    }
}
