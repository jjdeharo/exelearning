<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesLifeCycleGet.
 */
class OdeOldXmlCatalogPropertiesLifeCycleGet
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

    public static function oldElpCatalogLifeCyclePropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $propertyKeyAppend = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'lifeCycle']/@value")[0];
        $propertyCatalogLifeCycleKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomLifeCyclePropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'lifeCycle']/
        following-sibling::f:instance[1]");
        foreach ($lomLifeCyclePropertiesNodes as $lomLifeCyclePropertiesNode) {
            $contributionProperties = self::getLifeCycleContributionProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $statusProperties = self::getLifeCycleStatusProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $versionProperties = self::getVersionProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);

            $lomMetadataPropertiesArray = array_merge(
                $contributionProperties['odeProperties'],
                $statusProperties['odeProperties'],
                $versionProperties['odeProperties'],
            );
            foreach ($lomMetadataPropertiesArray as $lomMetadataProperty) {
                array_push($result['odeProperties'], $lomMetadataProperty);
            }

            // $catalogSub = $lomEducationalSubProperty->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.catalogSub']");
        }

        return $result;
    }

    private static function getLifeCycleStatusProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'status']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralLifeCycleStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'status']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralLifeCycleStatusPropertiesNodes as $lomGeneralLifeCycleStatusPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralLifeCycleStatusPropertiesNode)) {
                return $result;
            }

            $lomGeneralLifeCycleStatusPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralLifeCycleStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/@value");
            if (empty($propertyKeyAppend)) {
                return $result;
            } else {
                $propertyKeyAppend = $propertyKeyAppend[0];
            }
            $lomGeneralLifeCycleStatusValuePropertyKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;

            $lomGeneralLifeCycleStatusValuePropertiesNodeValue = $lomGeneralLifeCycleStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'value']/
            following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralLifeCycleStatusValuePropertiesNodeValue)) {
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                $odeProperties->setKey($lomGeneralLifeCycleStatusValuePropertyKey);
                $odeProperties->setValue($lomGeneralLifeCycleStatusValuePropertiesNodeValue[0]);
            }

            if (isset($odeProperties)) {
                array_push($result['odeProperties'], $odeProperties);
            }
        }

        return $result;
    }

    private static function getVersionProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'version']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }
        $propertyCatalogGeneralVersionKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralVersionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'version']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralVersionPropertiesNodes as $lomGeneralVersionPropertiesNode) {
            $lomGeneralVersionPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralVersionPropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value")[0];
            $lomGeneralVersionPropertyStringKey = $propertyCatalogGeneralVersionKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralVersionPropertyStringNodes = $lomGeneralVersionPropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            $i = 2;
            foreach ($lomGeneralVersionPropertyStringNodes as $key => $lomGeneralVersionPropertyStringNode) {
                $lomGeneralVersionPropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralVersionPropertyStringNodeValue = $lomGeneralVersionPropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralVersionPropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralVersionPropertyStringKey = $propertyCatalogGeneralVersionKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralVersionPropertyStringKey);
                    $odeProperties->setValue($lomGeneralVersionPropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralVersionStringPropertyLanguageAppendKey = $lomGeneralVersionPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralVersionPropertyLanguageKey = $propertyCatalogGeneralVersionKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralVersionStringPropertyLanguageAppendKey;
                $lomGeneralVersionStringPropertyLanguageNodeValue = $lomGeneralVersionPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralVersionStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralVersionPropertyLanguageKey = $propertyCatalogGeneralVersionKey.$i.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralVersionStringPropertyLanguageAppendKey;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralVersionPropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralVersionStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getLifeCycleContributionProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'contribute']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralContributionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'contribute']/
        following-sibling::f:list[1]");
        foreach ($lomGeneralContributionPropertiesNodes as $lomGeneralContributionPropertiesNode) {
            $lomGeneralContributionPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralContributionPropertiesNode->xpath("f:instance/
                f:dictionary/f:string[@value='date']/@value");
            if (empty($propertyKeyAppend)) {
                return $result;
            } else {
                $propertyKeyAppend = $propertyKeyAppend[0];
            }
            $lomGeneralContributionPropertyDateKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralContributionPropertyDateNodes = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeSub']/
                f:dictionary/f:string[@value='date']/following-sibling::f:instance[1]");
            foreach ($lomGeneralContributionPropertyDateNodes as $key => $lomGeneralContributionPropertyDateNode) {
                $i = 2;
                $lomGeneralContributionPropertyDateNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralContributionPropertyCatalogNodeValue = $lomGeneralContributionPropertyDateNode->xpath("f:dictionary/f:string[@value='dateTime']/
                    following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralContributionPropertyCatalogNodeValue)) {
                    // Get first value of array element (date part)
                    $dateValueArray = explode('T', $lomGeneralContributionPropertyCatalogNodeValue[0]);
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralContributionPropertyDateKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralContributionPropertyDateKey);
                    $odeProperties->setValue($dateValueArray[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
            $propertyKeyAppend = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeSub']/
                f:dictionary/f:string[@value='entity']/@value");
            if (empty($propertyKeyAppend)) {
                return $result;
            } else {
                $propertyKeyAppend = $propertyKeyAppend[0];
            }

            $lomGeneralContributionPropertyEntityKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralContributionPropertyEntityNodes = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeSub']/
                f:dictionary/f:string[@value='entity']/following-sibling::f:list[1]/f:instance");
            foreach ($lomGeneralContributionPropertyEntityNodes as $key => $lomGeneralContributionPropertyEntityNode) {
                $i = '2';
                $lomGeneralContributionPropertyEntityNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralContributionPropertyEntityNodeValue = $lomGeneralContributionPropertyEntityNode->xpath("f:dictionary/f:string[@value='valueOf_']/
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
                                            $lomGeneralContributionPropertyEntityChildKey = $lomGeneralContributionPropertyEntityKey.$entityChildKeyAppend;
                                            $odeProperties = new OdePropertiesSync();
                                            $odeProperties->setOdeSessionId($odeSessionId);
                                            if ($key >= '1') {
                                                $lomGeneralContributionPropertyEntityChildKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend.$entityChildKeyAppend;
                                            }
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
                    if ($key >= '1') {
                        ++$i;
                    }
                }
            }

            $propertyRoleKeyAppend = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeSub']/
                f:dictionary/f:string[@value='role']/@value")[0];
            $lomGeneralContributionPropertyRoleKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyRoleKeyAppend;
            $lomGeneralContributionPropertyRoleNodes = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeSub']/
                f:dictionary/f:string[@value='role']/following-sibling::f:instance");
            foreach ($lomGeneralContributionPropertyRoleNodes as $key => $lomGeneralContributionPropertyRoleNode) {
                $i = '2';
                $lomGeneralContributionPropertyRoleNode->registerXPathNamespace('f', $xpathNamespace);
                $propertyKeyAppend = $lomGeneralContributionPropertyRoleNode->xpath("f:dictionary/f:string[@value='value']/@value")[0];
                $lomGeneralContributionPropertyRoleValueKey = $lomGeneralContributionPropertyRoleKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                $lomGeneralContributionPropertyRoleNodeValue = $lomGeneralContributionPropertyRoleNode->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.roleValueSub']/
                f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");
                if (isset($lomGeneralContributionPropertyRoleNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralContributionPropertyRoleValueKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyRoleKeyAppend.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralContributionPropertyRoleValueKey);
                    $odeProperties->setValue($lomGeneralContributionPropertyRoleNodeValue[0]);
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
