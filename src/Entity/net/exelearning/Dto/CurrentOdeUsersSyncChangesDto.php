<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * CurrentOdeUsersDto.
 */
class CurrentOdeUsersSyncChangesDto extends BaseDto
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $odePageIdUpdate;

    /**
     * @var string
     */
    protected $odeBlockIdUpdate;

    /**
     * @var string
     */
    protected $odeComponentIdUpdate;

    /**
     * @var string
     */
    protected $destinationPageIdUpdate;

    /**
     * @var string
     */
    protected $styleThemeIdUpdate;

    /**
     * @var string
     */
    protected $actionTypeUpdate;

    /**
     * @var string
     */
    protected $userUpdate;

    /**
     * @return string
     */
    public function getOdePageIdUpdate()
    {
        return $this->odePageIdUpdate;
    }

    /**
     * @param string $odePageIdUpdate
     */
    public function setOdePageIdUpdate($odePageIdUpdate)
    {
        $this->odePageIdUpdate = $odePageIdUpdate;
    }

    /**
     * @return string
     */
    public function getOdeBlockIdUpdate()
    {
        return $this->odeBlockIdUpdate;
    }

    /**
     * @param string $odeBlockIdUpdate
     */
    public function setOdeBlockIdUpdate($odeBlockIdUpdate)
    {
        $this->odeBlockIdUpdate = $odeBlockIdUpdate;
    }

    /**
     * @return string
     */
    public function getOdeComponentIdUpdate()
    {
        return $this->odeComponentIdUpdate;
    }

    /**
     * @param string $odeComponentIdUpdate
     */
    public function setOdeComponentIdUpdate($odeComponentIdUpdate)
    {
        $this->odeComponentIdUpdate = $odeComponentIdUpdate;
    }

    /**
     * @return string
     */
    public function getDestinationPageIdUpdate()
    {
        return $this->destinationPageIdUpdate;
    }

    /**
     * @param string $destinationPageIdUpdate
     */
    public function setDestinationPageIdUpdate($destinationPageIdUpdate)
    {
        $this->destinationPageIdUpdate = $destinationPageIdUpdate;
    }

    /**
     * @return string
     */
    public function getStyleThemeIdUpdate()
    {
        return $this->styleThemeIdUpdate;
    }

    /**
     * @param string $styleThemeIdUpdate
     */
    public function setStyleThemeIdUpdate($styleThemeIdUpdate)
    {
        $this->styleThemeIdUpdate = $styleThemeIdUpdate;
    }

    /**
     * @return string
     */
    public function getActionTypeUpdate()
    {
        return $this->actionTypeUpdate;
    }

    /**
     * @param string $actionTypeUpdate
     */
    public function setActionTypeUpdate($actionTypeUpdate)
    {
        $this->actionTypeUpdate = $actionTypeUpdate;
    }

    /**
     * @return string
     */
    public function getUserUpdate()
    {
        return $this->userUpdate;
    }

    /**
     * @param string $userUpdate
     */
    public function setUserUpdate($userUpdate)
    {
        $this->userUpdate = $userUpdate;
    }
}
