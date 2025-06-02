<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdeNavStructureSyncListDto.
 */
class OdeNavStructureSyncListDto extends BaseDto
{
    /**
     * @var string
     */
    protected $odeSessionId;

    /**
     * @var OdeNavStructureSyncDto[]
     */
    protected $structure;

    public function __construct()
    {
        $this->structure = [];
    }

    /**
     * @return string
     */
    public function getOdeSessionId()
    {
        return $this->odeSessionId;
    }

    /**
     * @param string $odeSessionId
     */
    public function setOdeSessionId($odeSessionId)
    {
        $this->odeSessionId = $odeSessionId;
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\OdeNavStructureDto
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\OdeNavStructureDto $structure
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;
    }

    /**
     * @param OdeNavStructureSyncDto $structureElem
     */
    public function addStructure($structureElem)
    {
        $this->structure[] = $structureElem;
    }
}
