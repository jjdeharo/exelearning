<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdePropertiesSyncDto.
 */
class OdePropertiesSyncDto extends BaseDto
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
     * @var string
     */
    protected $category;

    /**
     * @var string
     */
    protected $multipleId;

    /**
     * @var int
     */
    protected $multipleIndex;

    protected array $groups = [];

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
     * @return string
     */
    public function getMultipleId()
    {
        return $this->multipleId;
    }

    public function setMultipleId($multipleId)
    {
        $this->multipleId = $multipleId;
    }

    /**
     * @return int
     */
    public function getMultipleIndex()
    {
        return $this->multipleIndex;
    }

    /**
     * @param string $multipleIndex
     */
    public function setMultipleIndex($multipleIndex)
    {
        $this->multipleIndex = $multipleIndex;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param string $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * Loads OdePropertiesDto from OdeProperties object.
     *
     * @param OdeProperties $odeProperties
     */
    public function loadFromEntity($odeProperties)
    {
        $this->setId($odeProperties->getId());
        $this->setKey($odeProperties->getKey());
        $this->setValue($odeProperties->getValue());
        $this->setCategory($odeProperties->getCategory());
        $this->setGroups($odeProperties->getGroups());
        $this->setMultipleId($odeProperties->getMultipleId());
        $this->setMultipleIndex($odeProperties->getMultipleIndex());
    }
}
