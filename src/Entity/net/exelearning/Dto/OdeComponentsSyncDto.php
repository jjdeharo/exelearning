<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\OdeComponentsSync;

/**
 * OdeComponentsSyncDto.
 */
class OdeComponentsSyncDto extends BaseDto
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $odePagStructureSyncId;

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
    protected $odeIdeviceId;

    /**
     * @var string
     */
    protected $odeIdeviceTypeName;

    /**
     * @var string
     */
    protected $htmlView;

    /**
     * @var string
     */
    protected $jsonProperties;

    /**
     * @var int
     */
    protected $order;

    /**
     * @var @var OdeComponentsSyncPropertiesDto[]
     */
    protected $odeComponentsSyncProperties;

    /**
     * @var @var BrokenLinks[]
     */
    protected $brokenLinks;

    /**
     * @var @var UsedFiles[]
     */
    protected $usedFiles;

    public function __construct()
    {
        $this->odeComponentsSyncProperties = [];
        $this->brokenLinks = [];
        $this->usedFiles = [];
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
    public function getOdeIdeviceId()
    {
        return $this->odeIdeviceId;
    }

    /**
     * @param string $odeIdeviceId
     */
    public function setOdeIdeviceId($odeIdeviceId)
    {
        $this->odeIdeviceId = $odeIdeviceId;
    }

    /**
     * @return string
     */
    public function getOdeIdeviceTypeName()
    {
        return $this->odeIdeviceTypeName;
    }

    /**
     * @param string $odeIdeviceTypeName
     */
    public function setOdeIdeviceTypeName($odeIdeviceTypeName)
    {
        $this->odeIdeviceTypeName = $odeIdeviceTypeName;
    }

    /**
     * @return string
     */
    public function getHtmlView()
    {
        return $this->htmlView;
    }

    /**
     * @param string $htmlView
     */
    public function setHtmlView($htmlView)
    {
        $this->htmlView = $htmlView;
    }

    /**
     * @return string
     */
    public function getJsonProperties()
    {
        return $this->jsonProperties;
    }

    /**
     * @param string $jsonProperties
     */
    public function setJsonProperties($jsonProperties)
    {
        $this->jsonProperties = $jsonProperties;
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
     * @return multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncPropertiesDto
     */
    public function getOdeComponentsSyncProperties()
    {
        return $this->odeComponentsSyncProperties;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncPropertiesDto $odeComponentsSyncProperties
     */
    public function setOdeComponentsSyncProperties($odeComponentsSyncProperties)
    {
        $this->odeComponentsSyncProperties = $odeComponentsSyncProperties;
    }

    /**
     * @param OdeComponentsSyncPropertiesDto $odeComponentsSyncProperties
     */
    public function addOdeComponentsSyncProperties($odeComponentsSyncProperties)
    {
        $this->odeComponentsSyncProperties[] = $odeComponentsSyncProperties;
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncBrokenLinksDto
     */
    public function getBrokenLinks()
    {
        return $this->brokenLinks;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncBrokenLinksDto $brokenLinks
     */
    public function setBrokenLinks($brokenLinks)
    {
        $this->brokenLinks = $brokenLinks;
    }

    /**
     * @param OdeComponentsSyncBrokenLinksDto $brokenLinks
     */
    public function addbrokenLinks($brokenLinks)
    {
        $this->brokenLinks[] = $brokenLinks;
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncBrokenLinksDto
     */
    public function getUsedFiles()
    {
        return $this->usedFiles;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdeComponentsSyncBrokenLinksDto $brokenLinks
     */
    public function setUsedFiles($usedFiles)
    {
        $this->usedFiles = $usedFiles;
    }

    /**
     * @param OdeComponentsSyncUsedFilesDto $usedFiles
     */
    public function addusedFiles($usedFiles)
    {
        $this->usedFiles[] = $usedFiles;
    }

    /**
     * Loads OdeComponentsSyncDto from OdeComponentsSync object.
     *
     * @param OdeComponentsSync $odeComponentsSync
     * @param bool              $loadOdeComponentsSyncProperties
     */
    public function loadFromEntity($odeComponentsSync, $loadOdeComponentsSyncProperties)
    {
        $this->setId($odeComponentsSync->getId());
        $this->setOdePagStructureSyncId($odeComponentsSync->getOdePagStructureSync()->getId());
        $this->setOdeSessionId($odeComponentsSync->getOdeSessionId());
        $this->setPageId($odeComponentsSync->getOdePageId());
        $this->setBlockId($odeComponentsSync->getOdeBlockId());
        $this->setOdeIdeviceId($odeComponentsSync->getOdeIdeviceId());
        $this->setOdeIdeviceTypeName($odeComponentsSync->getOdeIdeviceTypeName());
        $this->setHtmlView($odeComponentsSync->getHtmlView());
        $this->setJsonProperties($odeComponentsSync->getJsonProperties());
        $this->setOrder($odeComponentsSync->getOdeComponentsSyncOrder());

        // Load odeComponentsSyncProperties from config and database
        if ($loadOdeComponentsSyncProperties) {
            $odeComponentsSync->loadOdeComponentsSyncPropertiesFromConfig(); // initialize default values

            $odeComponentsSyncPropertiesDtos = [];

            foreach ($odeComponentsSync->getOdeComponentsSyncProperties() as $odeComponentsSyncProperties) {
                $odeComponentsSyncPropertiesDto = new OdeComponentsSyncPropertiesDto();
                $odeComponentsSyncPropertiesDto->loadFromEntity($odeComponentsSyncProperties);

                $odeComponentsSyncPropertiesDtos[$odeComponentsSyncProperties->getKey()] = $odeComponentsSyncPropertiesDto;
            }

            $this->setOdeComponentsSyncProperties($odeComponentsSyncPropertiesDtos);
        }
    }
}
