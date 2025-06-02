<?php

namespace App\Util\net\exelearning\Util;

/**
 * XmlUtil.
 *
 * Utility functions for working with Xml
 */
class XmlUtil
{
    /**
     * Returns an array with the contents of the xml string passed as param.
     *
     * @param string $xmlString
     *
     * @return array
     */
    public static function loadXmlStringToArray($xmlString)
    {
        $result = null;
        $xmlObject = simplexml_load_string($xmlString);
        $result = json_decode(json_encode($xmlObject), true);

        return $result;
    }
}
