<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesRightsGet.
 */
class OdeOldXmlCatalogPropertiesRightsGet
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

    public static function oldElpCatalogRightsPropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $propertyKeyAppend = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'rights']/@value")[0];
        $propertyCatalogRightsKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomRightsPropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'rights']/
        following-sibling::f:instance[@class='exe.engine.lom.lomsubs.rightsSub']");
        foreach ($lomRightsPropertiesNodes as $lomRightsPropertiesNode) {
            $copyrightAndOtherRestrictionsProperties = self::getCopyrightAndOtherRestrictionsProperties($odeSessionId, $lomRightsPropertiesNode, $xpathNamespace, $propertyCatalogRightsKey);
            $costProperties = self::getCostProperties($odeSessionId, $lomRightsPropertiesNode, $xpathNamespace, $propertyCatalogRightsKey);
            $descriptionProperties = self::getRightsDescriptionProperties($odeSessionId, $lomRightsPropertiesNode, $xpathNamespace, $propertyCatalogRightsKey);

            $lomMetadataPropertiesArray = array_merge(
                $copyrightAndOtherRestrictionsProperties['odeProperties'],
                $costProperties['odeProperties'],
                $descriptionProperties['odeProperties'],
            );
            foreach ($lomMetadataPropertiesArray as $lomMetadataProperty) {
                array_push($result['odeProperties'], $lomMetadataProperty);
            }

            // $catalogSub = $lomEducationalSubProperty->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.catalogSub']");
        }

        return $result;
    }

    private static function getCostProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'cost']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralCostPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'cost']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralCostPropertiesNodes as $lomGeneralCostPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralCostPropertiesNode)) {
                return $result;
            }

            $lomGeneralCostPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralCostPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/@value")[0];
            $lomGeneralCostValuePropertyKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;

            $lomGeneralCostValuePropertiesNodeValue = $lomGeneralCostPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/
            following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralCostValuePropertiesNodeValue)) {
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                $odeProperties->setKey($lomGeneralCostValuePropertyKey);
                $odeProperties->setValue($lomGeneralCostValuePropertiesNodeValue[0]);
            }

            if (isset($odeProperties)) {
                array_push($result['odeProperties'], $odeProperties);
            }
        }

        return $result;
    }

    private static function getRightsDescriptionProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'description']/@value")[0];
        $propertyCatalogGeneralRightsDescriptionKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralRightsDescriptionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'description']/
        following-sibling::f:instance[1]");
        $i = 2;
        foreach ($lomGeneralRightsDescriptionPropertiesNodes as $key => $lomGeneralRightsDescriptionPropertiesNode) {
            $lomGeneralRightsDescriptionPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralRightsDescriptionPropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value")[0];
            $lomGeneralDecriptionPropertyStringKey = $propertyCatalogGeneralRightsDescriptionKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralRightsDescriptionPropertyStringNodes = $lomGeneralRightsDescriptionPropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            foreach ($lomGeneralRightsDescriptionPropertyStringNodes as $lomGeneralRightsDescriptionPropertyStringNode) {
                $lomGeneralRightsDescriptionPropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralTitlePropertyStringNodeValue = $lomGeneralRightsDescriptionPropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitlePropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralDecriptionPropertyStringKey = $propertyCatalogGeneralRightsDescriptionKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralDecriptionPropertyStringKey);
                    $odeProperties->setValue($lomGeneralTitlePropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralTitleStringPropertyLanguageAppendKey = $lomGeneralRightsDescriptionPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralRightsDescriptionPropertyLanguageKey = $lomGeneralDecriptionPropertyStringKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralTitleStringPropertyLanguageAppendKey;
                $lomGeneralTitleStringPropertyLanguageNodeValue = $lomGeneralRightsDescriptionPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitleStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralRightsDescriptionPropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralTitleStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getCopyrightAndOtherRestrictionsProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'copyrightAndOtherRestrictions']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralCopyrightAndOtherRestrictionsPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'copyrightAndOtherRestrictions']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralCopyrightAndOtherRestrictionsPropertiesNodes as $lomGeneralCopyrightAndOtherRestrictionsPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralCopyrightAndOtherRestrictionsPropertiesNode)) {
                return $result;
            }

            $lomGeneralCopyrightAndOtherRestrictionsPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralCopyrightAndOtherRestrictionsPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/@value")[0];
            $lomGeneralCopyrightAndOtherRestrictionsValuePropertyKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;

            $lomGeneralCopyrightAndOtherRestrictionsValuePropertiesNodeValue = $lomGeneralCopyrightAndOtherRestrictionsPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/
            following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralCopyrightAndOtherRestrictionsValuePropertiesNodeValue)) {
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                $odeProperties->setKey($lomGeneralCopyrightAndOtherRestrictionsValuePropertyKey);
                $odeProperties->setValue($lomGeneralCopyrightAndOtherRestrictionsValuePropertiesNodeValue[0]);
            }

            if (isset($odeProperties)) {
                array_push($result['odeProperties'], $odeProperties);
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
