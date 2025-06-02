<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesRelationGet.
 */
class OdeOldXmlCatalogPropertiesRelationGet
{
    // metadata entity values tag name
    public const ENTITY_METADATA_TAG_VALUES =
        [
            'organization' => 'ORG',
            'name' => 'FN',
            'email' => 'EMAIL;TYPE=INTERNET',
        ];
    // duration values tag name
    public const DURATION_DATE_TAG_VALUES =
        [
            'years' => 'Y',
            'months' => 'M',
            'days' => 'D',
        ];
    public const DURATION_TIME_TAG_VALUES =
        [
            'hours' => 'H',
            'minutes' => 'M',
            'seconds' => 'S',
        ];
    // metadata schema values tag name
    public const METADATA_SCHEMA_VALUES =
        [
            'LOM' => 'LOMv1.0',
            'LOM-ES' => 'LOM-ESv1.0',
        ];
    public const ODE_XML_KEY_UNDERSCORE_POSITION = 0;
    public const ODE_XML_KEY_UNDERSCORE = '_';
    public const ODE_XML_KEY_PREFIX = 'pp';
    public const ODE_XML_TAG_FIELD_KEY = 'key';
    public const ODE_XML_TAG_FIELD_VALUE = 'value';
    public const OLD_ODE_XML_INSTANCE = 'instance';
    public const OLD_ODE_XML_DICTIONARY = 'dictionary';
    public const OLD_ODE_XML_LIST = 'list';
    public const OLD_ODE_XML_UNICODE = 'unicode';
    public const OLD_ODE_XML_ATTRIBUTES = '@attributes';
    // const OLD_ODE_XML_IDEVICE_TEXT = 'instance';
    public const OLD_ODE_XML_IDEVICE_TEXT_CONTENT = 'string role="key" value="content_w_resourcePaths"';

    public static function oldElpCatalogRelationPropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $propertyKeyAppend = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'relation']/@value")[0];
        $propertyCatalogLifeCycleKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomLifeCyclePropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'relation']/
        following-sibling::f:list[1]/f:instance");
        foreach ($lomLifeCyclePropertiesNodes as $lomLifeCyclePropertiesNode) {
            $kindProperties = self::getKindProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $resourceProperties = self::getResourceProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);

            $lomMetadataPropertiesArray = array_merge(
                $kindProperties['odeProperties'],
                $resourceProperties['odeProperties'],
            );
            foreach ($lomMetadataPropertiesArray as $lomMetadataProperty) {
                array_push($result['odeProperties'], $lomMetadataProperty);
            }

            // $catalogSub = $lomEducationalSubProperty->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.catalogSub']");
        }

        return $result;
    }

    private static function getKindProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'kind']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDateStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'kind']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralDateStatusPropertiesNodes as $lomGeneralDateStatusPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralDateStatusPropertiesNode)) {
                return $result;
            }

            $lomGeneralDateStatusPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/@value")[0];
            $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;

            $lomGeneralDateStatusValuePropertiesNodeValue = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/
            following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralDateStatusValuePropertiesNodeValue)) {
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                $odeProperties->setKey($lomGeneralDateStatusValuePropertyKey);
                $odeProperties->setValue($lomGeneralDateStatusValuePropertiesNodeValue[0]);
            }

            if (isset($odeProperties)) {
                array_push($result['odeProperties'], $odeProperties);
            }
        }

        return $result;
    }

    private static function getResourceProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'resource']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralIdentfierPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'resource']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralIdentfierPropertiesNodes as $lomGeneralIdentfierPropertiesNode) {
            // Return empty array if no identifier
            if (empty($lomGeneralIdentfierPropertiesNode)) {
                return $result;
            }

            $lomGeneralIdentfierPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='identifier']/@value")[0];
            $lomGeneralIdentfierPropertyKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='identifier']/following-sibling::f:instance[1]");
            foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                $propertyKeyAppend = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='catalog']/@value");
                if (empty($propertyKeyAppend)) {
                    return $result;
                } else {
                    $propertyKeyAppend = $propertyKeyAppend[0];
                }
                $lomGeneralIdentfierPropertyCatalogKey = $lomGeneralIdentfierPropertyKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='catalog']/
                following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralIdentfierPropertyCatalogNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    $odeProperties->setKey($lomGeneralIdentfierPropertyCatalogKey);
                    $odeProperties->setValue($lomGeneralIdentfierPropertyCatalogNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                $propertyKeyAppend = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='entry']/@value");
                if (empty($propertyKeyAppend)) {
                    return $result;
                } else {
                    $propertyKeyAppend = $propertyKeyAppend[0];
                }
                $lomGeneralIdentfierPropertyCatalogKey = $lomGeneralIdentfierPropertyKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='entry']/
                following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralIdentfierPropertyCatalogNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    $odeProperties->setKey($lomGeneralIdentfierPropertyCatalogKey);
                    $odeProperties->setValue($lomGeneralIdentfierPropertyCatalogNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    /**
     * Applies the replaces passed as param.
     *
     * @param array  $replaces
     * @param string $text
     */
    private static function applyReplaces($replaces, $text)
    {
        $result = $text;

        foreach ($replaces as $search => $replace) {
            $result = str_replace($search, $replace, $result);
        }

        return $result;
    }
}
