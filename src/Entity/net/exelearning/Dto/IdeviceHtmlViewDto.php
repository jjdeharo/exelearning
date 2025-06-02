<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * IdeviceHtmlViewDto.
 */
class IdeviceHtmlViewDto extends BaseDto
{
    /**
     * @var string
     */
    protected $odeComponentsSyncId;

    /**
     * @var string
     */
    protected $htmlView;

    /**
     * @return string
     */
    public function getOdeComponentsSyncId()
    {
        return $this->odeComponentsSyncId;
    }

    /**
     * @param string $odeComponentsSyncId
     */
    public function setOdeComponentsSyncId($odeComponentsSyncId)
    {
        $this->odeComponentsSyncId = $odeComponentsSyncId;
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
}
