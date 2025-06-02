<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\OdeFiles;
use App\Util\net\exelearning\Util\FileUtil;

/**
 * OdeFilesDto.
 */
class OdeFilesDto extends BaseDto
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
    protected $odePlatformId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $versionName;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var string
     */
    protected $sizeFormatted;

    /**
     * @var bool
     */
    protected $isManualSave;

    /**
     * @var string
     */
    protected $updatedAt;

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
    public function getOdePlatformId()
    {
        return $this->odePlatformId;
    }

    public function setOdePlatformId($odePlatformId)
    {
        $this->odePlatformId = $odePlatformId;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getVersionName()
    {
        return $this->versionName;
    }

    /**
     * @param string $versionName
     */
    public function setVersionName($versionName)
    {
        $this->versionName = $versionName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getIsManualSave()
    {
        return $this->isManualSave;
    }

    /**
     * @param string $isManualSave
     */
    public function setIsManualSave($isManualSave)
    {
        $this->isManualSave = $isManualSave;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getSizeFormatted()
    {
        return $this->sizeFormatted;
    }

    /**
     * @param string $sizeFormatted
     */
    public function setSizeFormatted($sizeFormatted)
    {
        $this->sizeFormatted = $sizeFormatted;
    }

    /**
     * Loads OdeFilesDto from OdeFiles object.
     *
     * @param OdeFiles $odeFiles
     */
    public function loadFromEntity($odeFiles)
    {
        $this->setId($odeFiles->getId());
        $this->setOdeId($odeFiles->getOdeId());
        $this->setOdeVersionId($odeFiles->getOdeVersionId());
        $this->setOdePlatformId($odeFiles->getOdePlatformId());
        $this->setTitle($odeFiles->getTitle());
        $this->setVersionName($odeFiles->getVersionName());
        $this->setFileName($odeFiles->getFileName());
        $this->setSize($odeFiles->getSize());
        $this->setIsManualSave($odeFiles->getIsManualSave());
        $this->setUpdatedAt($odeFiles->getUpdatedAt());

        // Get formated size
        $this->setSizeFormatted(FileUtil::formatFilesize($this->getSize()));
    }
}
