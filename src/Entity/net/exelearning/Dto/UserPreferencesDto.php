<?php

namespace App\Entity\net\exelearning\Dto;

use App\Entity\net\exelearning\Entity\UserPreferences;

/**
 * UserPreferencesDto.
 */
class UserPreferencesDto extends BaseDto
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
     * Loads UserPreferencesDto from UserPreferences object.
     *
     * @param UserPreferences $UserPreferences
     */
    public function loadFromEntity($UserPreferences)
    {
        $this->setId($UserPreferences->getId());
        $this->setKey($UserPreferences->getKey());
        $this->setValue($UserPreferences->getValue());
    }
}
