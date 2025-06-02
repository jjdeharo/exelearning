<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdeComponentsSyncDto.
 */
class OdeComponentsSyncUsedFilesDto extends BaseDto
{
    /**
     * @var string
     */
    protected $usedFiles;

    /**
     * @var string
     */
    protected $usedFilesPath;

    /**
     * @var string
     */
    protected $usedFilesSize;

    /**
     * @var string
     */
    protected $pageNamesUsedFiles;

    /**
     * @var string
     */
    protected $blockNamesUsedFiles;

    /**
     * @var string
     */
    protected $typeComponentSyncUsedFiles;

    /**
     * @var string
     */
    protected $orderComponentSyncUsedFiles;

    /**
     * @return string
     */
    public function getUsedFiles()
    {
        return $this->usedFiles;
    }

    /**
     * @param string $usedFiles
     */
    public function setUsedFiles($usedFiles)
    {
        $this->usedFiles = $usedFiles;
    }

    /**
     * @return string
     */
    public function getUsedFilesPath()
    {
        return $this->usedFilesPath;
    }

    /**
     * @param string $usedFilesPath
     */
    public function setUsedFilesPath($usedFilesPath)
    {
        $this->usedFilesPath = $usedFilesPath;
    }

    /**
     * @return string
     */
    public function getUsedFilesSize()
    {
        return $this->usedFilesSize;
    }

    /**
     * @param string $usedFilesSize
     */
    public function setUsedFilesSize($usedFilesSize)
    {
        $this->usedFilesSize = $usedFilesSize;
    }

    /**
     * @return string
     */
    public function getPageNamesUsedFiles()
    {
        return $this->pageNamesUsedFiles;
    }

    /**
     * @param string $pageNamesUsedFiles
     */
    public function setPageNamesUsedFiles($pageNamesUsedFiles)
    {
        $this->pageNamesUsedFiles = $pageNamesUsedFiles;
    }

    /**
     * @return string
     */
    public function getBlockNamesUsedFiles()
    {
        return $this->blockNamesUsedFiles;
    }

    /**
     * @param string $blockNamesUsedFiles
     */
    public function setBlockNamesUsedFiles($blockNamesUsedFiles)
    {
        $this->blockNamesUsedFiles = $blockNamesUsedFiles;
    }

    /**
     * @return string
     */
    public function getTypeComponentSyncUsedFiles()
    {
        return $this->typeComponentSyncUsedFiles;
    }

    /**
     * @param string $typeComponentSyncUsedFiles
     */
    public function setTypeComponentSyncUsedFiles($typeComponentSyncUsedFiles)
    {
        $this->typeComponentSyncUsedFiles = $typeComponentSyncUsedFiles;
    }

    /**
     * @return string
     */
    public function getOrderComponentSyncUsedFiles()
    {
        return $this->orderComponentSyncUsedFiles;
    }

    /**
     * @param string $orderComponentSyncUsedFiles
     */
    public function setOrderComponentSyncUsedFiles($orderComponentSyncUsedFiles)
    {
        $this->orderComponentSyncUsedFiles = $orderComponentSyncUsedFiles;
    }
}
