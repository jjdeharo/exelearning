<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesAnnotationGet.
 */
class OdeOldXmlCatalogPropertiesAnnotationGet
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

    public static function oldElpCatalogAnnotationPropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $propertyKeyAppend = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'annotation']/@value")[0];
        $propertyCatalogLifeCycleKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomLifeCyclePropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'annotation']/
        following-sibling::f:list[1]/f:instance");
        foreach ($lomLifeCyclePropertiesNodes as $lomLifeCyclePropertiesNode) {
            $dateProperties = self::getDateProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $descriptionProperties = self::getAnnotationDescriptionProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $entityProperties = self::getEntityProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);

            $lomMetadataPropertiesArray = array_merge(
                $dateProperties['odeProperties'],
                $descriptionProperties['odeProperties'],
                $entityProperties['odeProperties'],
            );
            foreach ($lomMetadataPropertiesArray as $lomMetadataProperty) {
                array_push($result['odeProperties'], $lomMetadataProperty);
            }

            // $catalogSub = $lomEducationalSubProperty->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.catalogSub']");
        }

        return $result;
    }

    private static function getDateProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'date']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDateStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'date']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralDateStatusPropertiesNodes as $lomGeneralDateStatusPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralDateStatusPropertiesNode)) {
                return $result;
            }

            $lomGeneralDateStatusPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            // $propertyKeyAppend = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'dateTime']/@value")[0];
            $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey;

            $lomGeneralDateStatusValuePropertiesNodeValue = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'dateTime']/
            following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralDateStatusValuePropertiesNodeValue)) {
                // Get first value of array element (date part)
                $dateValueArray = explode('T', $lomGeneralDateStatusValuePropertiesNodeValue[0]);
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                $odeProperties->setKey($lomGeneralDateStatusValuePropertyKey);
                $odeProperties->setValue($dateValueArray[0]);
            }

            if (isset($odeProperties)) {
                array_push($result['odeProperties'], $odeProperties);
            }
        }

        return $result;
    }

    private static function getAnnotationDescriptionProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'description']/@value")[0];
        $propertyCatalogGeneralDescriptionKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDescriptionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'description']/
        following-sibling::f:instance[1]");
        $i = 2;
        foreach ($lomGeneralDescriptionPropertiesNodes as $key => $lomGeneralDescriptionPropertiesNode) {
            $lomGeneralDescriptionPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralDescriptionPropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value")[0];
            $lomGeneralDecriptionPropertyStringKey = $propertyCatalogGeneralDescriptionKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralDescriptionPropertyStringNodes = $lomGeneralDescriptionPropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            foreach ($lomGeneralDescriptionPropertyStringNodes as $lomGeneralDescriptionPropertyStringNode) {
                $lomGeneralDescriptionPropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralTitlePropertyStringNodeValue = $lomGeneralDescriptionPropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitlePropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralDecriptionPropertyStringKey = $propertyCatalogGeneralDescriptionKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralDecriptionPropertyStringKey);
                    $odeProperties->setValue($lomGeneralTitlePropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralTitleStringPropertyLanguageAppendKey = $lomGeneralDescriptionPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralDescriptionPropertyLanguageKey = $lomGeneralDecriptionPropertyStringKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralTitleStringPropertyLanguageAppendKey;
                $lomGeneralTitleStringPropertyLanguageNodeValue = $lomGeneralDescriptionPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitleStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralDescriptionPropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralTitleStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getEntityProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'entity']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralContributionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'entity']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralContributionPropertiesNodes as $lomGeneralContributionPropertiesNode) {
            $lomGeneralContributionPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $lomGeneralContributionPropertyEntityNodeValue = $lomGeneralContributionPropertiesNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralContributionPropertyEntityNodeValue)) {
                // Get array of different values from entity with tag name
                $entityArrayValues = explode(' ', $lomGeneralContributionPropertyEntityNodeValue[0]);

                // Get values from different entities name
                foreach ($entityArrayValues as $entityArrayValue) {
                    foreach (self::ENTITY_METADATA_TAG_VALUES as $entityChildTagValue) {
                        if (str_contains($entityArrayValue, $entityChildTagValue)) {
                            // Get entity child array of tag name and value
                            $entityChildArrayValues = explode(':', $entityArrayValue);
                            if (isset($entityChildArrayValues[1])) {
                                foreach (self::ENTITY_METADATA_TAG_VALUES as $entityChildKey => $value) {
                                    if (str_contains($value, $entityChildArrayValues[0])) {
                                        $entityChildKeyAppend = self::ODE_XML_KEY_UNDERSCORE.$entityChildKey;
                                        $lomGeneralContributionPropertyEntityChildKey = $propertyCatalogKey.$entityChildKeyAppend;
                                        $odeProperties = new OdePropertiesSync();
                                        $odeProperties->setOdeSessionId($odeSessionId);
                                        $odeProperties->setKey($lomGeneralContributionPropertyEntityChildKey);
                                        $odeProperties->setValue($entityChildArrayValues[1]);

                                        if (isset($odeProperties)) {
                                            array_push($result['odeProperties'], $odeProperties);
                                        }
                                    }
                                }
                            }
                        }
                    }
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
