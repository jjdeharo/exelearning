<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdeLastUpdatedDto.
 */
class OdeLastUpdatedDto extends BaseDto
{
    /**
     * @var string
     */
    protected $odeId;

    /**
     * @var string
     */
    protected $lastUpdatedDate;

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
     * @return string timestamp
     */
    public function getLastUpdatedDate()
    {
        return $this->lastUpdatedDate;
    }

    /**
     * @param string timestamp $lastUpdatedDate
     */
    public function setLastUpdatedDate($lastUpdatedDate)
    {
        $this->lastUpdatedDate = $lastUpdatedDate;
    }
}
