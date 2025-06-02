<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdeComponentsSyncDto.
 */
class OdeComponentsSyncBrokenLinksDto extends BaseDto
{
    /**
     * @var string
     */
    protected $brokenLinks;

    /**
     * @var string
     */
    protected $brokenLinksError;

    /**
     * @var string
     */
    protected $nTimesBrokenLinks;

    /**
     * @var string
     */
    protected $pageNamesBrokenLinks;

    /**
     * @var string
     */
    protected $blockNamesBrokenLinks;

    /**
     * @var string
     */
    protected $typeComponentSyncBrokenLinks;

    /**
     * @var string
     */
    protected $orderComponentSyncBrokenLinks;

    /**
     * @return string
     */
    public function getBrokenLinks()
    {
        return $this->brokenLinks;
    }

    /**
     * @param string $brokenLinks
     */
    public function setBrokenLinks($brokenLinks)
    {
        $this->brokenLinks = $brokenLinks;
    }

    /**
     * @return string
     */
    public function getBrokenLinksError()
    {
        return $this->brokenLinksError;
    }

    /**
     * @param string $brokenLinksError
     */
    public function setBrokenLinksError($brokenLinksError)
    {
        $this->brokenLinksError = $brokenLinksError;
    }

    /**
     * @return string
     */
    public function getNTimesBrokenLinks()
    {
        return $this->nTimesBrokenLinks;
    }

    /**
     * @param string $nTimesBrokenLinks
     */
    public function setNTimesBrokenLinks($nTimesBrokenLinks)
    {
        $this->nTimesBrokenLinks = $nTimesBrokenLinks;
    }

    /**
     * @return string
     */
    public function getPageNamesBrokenLinks()
    {
        return $this->pageNamesBrokenLinks;
    }

    /**
     * @param string $pageNamesBrokenLinks
     */
    public function setPageNamesBrokenLinks($pageNamesBrokenLinks)
    {
        $this->pageNamesBrokenLinks = $pageNamesBrokenLinks;
    }

    /**
     * @return string
     */
    public function getBlockNamesBrokenLinks()
    {
        return $this->blockNamesBrokenLinks;
    }

    /**
     * @param string $blockNamesBrokenLinks
     */
    public function setBlockNamesBrokenLinks($blockNamesBrokenLinks)
    {
        $this->blockNamesBrokenLinks = $blockNamesBrokenLinks;
    }

    /**
     * @return string
     */
    public function getTypeComponentSyncBrokenLinks()
    {
        return $this->typeComponentSyncBrokenLinks;
    }

    /**
     * @param string $typeComponentSyncBrokenLinks
     */
    public function setTypeComponentSyncBrokenLinks($typeComponentSyncBrokenLinks)
    {
        $this->typeComponentSyncBrokenLinks = $typeComponentSyncBrokenLinks;
    }

    /**
     * @return string
     */
    public function getOrderComponentSyncBrokenLinks()
    {
        return $this->orderComponentSyncBrokenLinks;
    }

    /**
     * @param string $orderComponentSyncBrokenLinks
     */
    public function setOrderComponentSyncBrokenLinks($orderComponentSyncBrokenLinks)
    {
        $this->orderComponentSyncBrokenLinks = $orderComponentSyncBrokenLinks;
    }
}
