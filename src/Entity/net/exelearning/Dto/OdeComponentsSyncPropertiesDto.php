<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\OdeComponentsSyncProperties;

/**
 * OdeComponentsSyncPropertiesDto.
 */
class OdeComponentsSyncPropertiesDto extends BaseDto
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
     * Loads OdeComponentsSyncPropertiesDto from OdeComponentsSyncProperties object.
     *
     * @param OdeComponentsSyncProperties $odeComponentsSyncProperties
     */
    public function loadFromEntity($odeComponentsSyncProperties)
    {
        $this->setId($odeComponentsSyncProperties->getId());
        $this->setKey($odeComponentsSyncProperties->getKey());
        $this->setValue($odeComponentsSyncProperties->getValue());
    }
}
