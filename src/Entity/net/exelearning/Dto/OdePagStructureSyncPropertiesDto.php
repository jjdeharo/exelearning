<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\OdePagStructureSyncProperties;

/**
 * OdePagStructureSyncPropertiesDto.
 */
class OdePagStructureSyncPropertiesDto extends BaseDto
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Loads OdePagStructureSyncPropertiesDto from OdePagStructureSyncProperties object.
     *
     * @param OdePagStructureSyncProperties $odePagStructureSyncProperties
     */
    public function loadFromEntity($odePagStructureSyncProperties)
    {
        $this->setId($odePagStructureSyncProperties->getId());
        $this->setKey($odePagStructureSyncProperties->getKey());
        $this->setValue($odePagStructureSyncProperties->getValue());
    }
}
