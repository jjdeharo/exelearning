<?php

namespace App\Entity\net\exelearning\Dto\Internal;

use App\Entity\net\exelearning\Dto\BaseDto;

/**
 * OdeSaveDto.
 */
class OdeSaveDto extends BaseDto
{
    /**
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * @var string[]
     */
    protected $odeComponentsMapping;

    public function __construct()
    {
        $this->odeComponentsMapping = [];
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return array
     */
    public function getOdeComponentsMapping()
    {
        return $this->odeComponentsMapping;
    }

    /**
     * @param array $odeComponentsMapping
     */
    public function setOdeComponentsMapping($odeComponentsMapping)
    {
        $this->odeComponentsMapping = $odeComponentsMapping;
    }

    /**
     * @param string $odeComponentsMapping
     */
    public function addOdeComponentsMapping($odeComponentsMapping)
    {
        $this->odeComponentsMapping[] = $odeComponentsMapping;
    }
}
