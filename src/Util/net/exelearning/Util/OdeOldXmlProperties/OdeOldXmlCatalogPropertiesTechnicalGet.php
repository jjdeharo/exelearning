<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesTechnicalGet.
 */
class OdeOldXmlCatalogPropertiesTechnicalGet
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

    public static function oldElpCatalogTechnicalPropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $propertyKeyAppend = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'technical']/@value")[0];
        $propertyCatalogLifeCycleKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomLifeCyclePropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'technical']/
        following-sibling::f:instance[1]");
        foreach ($lomLifeCyclePropertiesNodes as $lomLifeCyclePropertiesNode) {
            $durationProperties = self::getDurationProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $formatProperties = self::getFormatProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $installationRemarksProperties = self::getInstallationRemarksProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $otherPlatformRequirementsProperties = self::getOtherPlatformRequirementsProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $sizeProperties = self::getSizeProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $locationProperties = self::getLocationProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $requirementProperties = self::getRequirementProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);

            $lomMetadataPropertiesArray = array_merge(
                $durationProperties['odeProperties'],
                $formatProperties['odeProperties'],
                $installationRemarksProperties['odeProperties'],
                $otherPlatformRequirementsProperties['odeProperties'],
                $sizeProperties['odeProperties'],
                $locationProperties['odeProperties'],
                $requirementProperties['odeProperties'],
            );
            foreach ($lomMetadataPropertiesArray as $lomMetadataProperty) {
                array_push($result['odeProperties'], $lomMetadataProperty);
            }

            // $catalogSub = $lomEducationalSubProperty->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.catalogSub']");
        }

        return $result;
    }

    private static function getFormatProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'format']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDateStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'format']/
        following-sibling::f:list[1]/f:instance");
        $i = 2;
        foreach ($lomGeneralDateStatusPropertiesNodes as $key => $lomGeneralDateStatusPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralDateStatusPropertiesNode)) {
                return $result;
            }

            $lomGeneralDateStatusPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            // $propertyKeyAppend = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'dateTime']/@value")[0];
            $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey;

            $lomGeneralDateStatusValuePropertiesNodeValue = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralDateStatusValuePropertiesNodeValue)) {
                if ($key >= '1') {
                    $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    ++$i;
                }
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

    private static function getInstallationRemarksProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'installationRemarks']/@value")[0];
        $propertyCatalogGeneralInstallationRemarksKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralInstallationRemarksPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'installationRemarks']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralInstallationRemarksPropertiesNodes as $lomGeneralInstallationRemarksPropertiesNode) {
            $lomGeneralInstallationRemarksPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralInstallationRemarksPropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value");
            if (empty($propertyKeyAppend)) {
                return $result;
            } else {
                $propertyKeyAppend = $propertyKeyAppend[0];
            }
            $lomGeneralInstallationRemarksPropertyStringKey = $propertyCatalogGeneralInstallationRemarksKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralInstallationRemarksPropertyStringNodes = $lomGeneralInstallationRemarksPropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            $i = 2;
            foreach ($lomGeneralInstallationRemarksPropertyStringNodes as $key => $lomGeneralInstallationRemarksPropertyStringNode) {
                $lomGeneralInstallationRemarksPropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralInstallationRemarksPropertyStringNodeValue = $lomGeneralInstallationRemarksPropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralInstallationRemarksPropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralInstallationRemarksPropertyStringKey = $propertyCatalogGeneralInstallationRemarksKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralInstallationRemarksPropertyStringKey);
                    $odeProperties->setValue($lomGeneralInstallationRemarksPropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralInstallationRemarksStringPropertyLanguageAppendKey = $lomGeneralInstallationRemarksPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralInstallationRemarksPropertyLanguageKey = $propertyCatalogGeneralInstallationRemarksKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralInstallationRemarksStringPropertyLanguageAppendKey;
                $lomGeneralInstallationRemarksStringPropertyLanguageNodeValue = $lomGeneralInstallationRemarksPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralInstallationRemarksStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralInstallationRemarksPropertyLanguageKey = $propertyCatalogGeneralInstallationRemarksKey.$i.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralInstallationRemarksStringPropertyLanguageAppendKey;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralInstallationRemarksPropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralInstallationRemarksStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getOtherPlatformRequirementsProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'otherPlatformRequirements']/@value")[0];
        $propertyCatalogGeneralOtherPlatformRequirementsKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralOtherPlatformRequirementsPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'otherPlatformRequirements']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralOtherPlatformRequirementsPropertiesNodes as $lomGeneralOtherPlatformRequirementsPropertiesNode) {
            $lomGeneralOtherPlatformRequirementsPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralOtherPlatformRequirementsPropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value");
            if (empty($propertyKeyAppend)) {
                return $result;
            } else {
                $propertyKeyAppend = $propertyKeyAppend[0];
            }
            $lomGeneralOtherPlatformRequirementsPropertyStringKey = $propertyCatalogGeneralOtherPlatformRequirementsKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralOtherPlatformRequirementsPropertyStringNodes = $lomGeneralOtherPlatformRequirementsPropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            $i = 2;
            foreach ($lomGeneralOtherPlatformRequirementsPropertyStringNodes as $key => $lomGeneralOtherPlatformRequirementsPropertyStringNode) {
                $lomGeneralOtherPlatformRequirementsPropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralOtherPlatformRequirementsPropertyStringNodeValue = $lomGeneralOtherPlatformRequirementsPropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralOtherPlatformRequirementsPropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralOtherPlatformRequirementsPropertyStringKey = $propertyCatalogGeneralOtherPlatformRequirementsKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralOtherPlatformRequirementsPropertyStringKey);
                    $odeProperties->setValue($lomGeneralOtherPlatformRequirementsPropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralOtherPlatformRequirementsStringPropertyLanguageAppendKey = $lomGeneralOtherPlatformRequirementsPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralOtherPlatformRequirementsPropertyLanguageKey = $propertyCatalogGeneralOtherPlatformRequirementsKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralOtherPlatformRequirementsStringPropertyLanguageAppendKey;
                $lomGeneralOtherPlatformRequirementsStringPropertyLanguageNodeValue = $lomGeneralOtherPlatformRequirementsPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralOtherPlatformRequirementsStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralOtherPlatformRequirementsPropertyLanguageKey = $propertyCatalogGeneralOtherPlatformRequirementsKey.$i.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralOtherPlatformRequirementsStringPropertyLanguageAppendKey;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralOtherPlatformRequirementsPropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralOtherPlatformRequirementsStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getSizeProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'size']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDateStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'size']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralDateStatusPropertiesNodes as $lomGeneralDateStatusPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralDateStatusPropertiesNode)) {
                return $result;
            }

            $lomGeneralDateStatusPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            // $propertyKeyAppend = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'dateTime']/@value")[0];
            $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey;

            $lomGeneralDateStatusValuePropertiesNodeValue = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

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

    private static function getLocationProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'location']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDateStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'location']/
        following-sibling::f:list[1]/f:instance");
        $i = 2;
        foreach ($lomGeneralDateStatusPropertiesNodes as $key => $lomGeneralDateStatusPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralDateStatusPropertiesNode)) {
                return $result;
            }

            $lomGeneralDateStatusPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            // $propertyKeyAppend = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'dateTime']/@value")[0];
            $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey;

            $lomGeneralDateStatusValuePropertiesNodeValue = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralDateStatusValuePropertiesNodeValue)) {
                if ($key >= '1') {
                    $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    ++$i;
                }
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

    private static function getRequirementProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'requirement']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralIdentfierPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'requirement']/
        following-sibling::f:list[1]/f:instance");
        foreach ($lomGeneralIdentfierPropertiesNodes as $lomGeneralIdentfierPropertiesNode) {
            // Return empty array if no identifier
            if (empty($lomGeneralIdentfierPropertiesNode)) {
                return $result;
            }

            $lomGeneralIdentfierPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/@value")[0];
            $lomGeneralIdentfierPropertyCatalogKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/following-sibling::f:list[1]/f:instance");
            $i = 2;
            foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralIdentfierPropertyCatalogKeyMaxVersion = $lomGeneralIdentfierPropertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='maximumVersion']/@value")[0];
                $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='maximumVersion']/
                    following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralIdentfierPropertyCatalogNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralIdentfierPropertyCatalogKeyMaxVersion = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralIdentfierPropertyCatalogKeyMaxVersion);
                    $odeProperties->setValue($lomGeneralIdentfierPropertyCatalogNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/@value")[0];
            $lomGeneralIdentfierPropertyCatalogKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/following-sibling::f:list[1]/f:instance");
            $i = 2;
            foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralIdentfierPropertyCatalogKeyMaxVersion = $lomGeneralIdentfierPropertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='minimumVersion']/@value")[0];
                $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='minimumVersion']/
                    following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralIdentfierPropertyCatalogNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralIdentfierPropertyCatalogKeyMaxVersion = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralIdentfierPropertyCatalogKeyMaxVersion);
                    $odeProperties->setValue($lomGeneralIdentfierPropertyCatalogNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }

            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/@value")[0];
            $lomGeneralIdentfierPropertyCatalogKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/following-sibling::f:list[1]/f:instance");
            foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                $propertyKeyAppend = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='name']/@value")[0];
                $lomGeneralIdentfierPropertyCatalogKey = $lomGeneralIdentfierPropertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='name']/following-sibling::f:instance[1]");
                $i = 2;
                foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                    $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                    $lomGeneralIdentfierPropertyCatalogKeyName = $lomGeneralIdentfierPropertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='value']/@value")[0];
                    $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='value']/
                    following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

                    if (!empty($lomGeneralIdentfierPropertyCatalogNodeValue)) {
                        $odeProperties = new OdePropertiesSync();
                        $odeProperties->setOdeSessionId($odeSessionId);
                        if ($key >= '1') {
                            $lomGeneralIdentfierPropertyCatalogKeyName = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                            ++$i;
                        }
                        $odeProperties->setKey($lomGeneralIdentfierPropertyCatalogKeyName);
                        $odeProperties->setValue($lomGeneralIdentfierPropertyCatalogNodeValue[0]);
                    }

                    if (isset($odeProperties)) {
                        array_push($result['odeProperties'], $odeProperties);
                    }
                }
            }

            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/@value")[0];
            $lomGeneralIdentfierPropertyCatalogKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='orComposite']/following-sibling::f:list[1]/f:instance");
            foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                $propertyKeyAppend = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='type_']/@value")[0];
                $lomGeneralIdentfierPropertyCatalogKey = $lomGeneralIdentfierPropertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='type_']/following-sibling::f:instance[1]");
                $i = 2;
                foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                    $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                    $lomGeneralIdentfierPropertyCatalogKeyName = $lomGeneralIdentfierPropertyCatalogKey.$lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='value']/@value")[0];
                    $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='value']/
                    following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

                    if (!empty($lomGeneralIdentfierPropertyCatalogNodeValue)) {
                        $odeProperties = new OdePropertiesSync();
                        $odeProperties->setOdeSessionId($odeSessionId);
                        if ($key >= '1') {
                            $lomGeneralIdentfierPropertyCatalogKeyName = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                            ++$i;
                        }
                        $odeProperties->setKey($lomGeneralIdentfierPropertyCatalogKeyName);
                        $odeProperties->setValue($lomGeneralIdentfierPropertyCatalogNodeValue[0]);
                    }

                    if (isset($odeProperties)) {
                        array_push($result['odeProperties'], $odeProperties);
                    }
                }
            }
        }

        return $result;
    }

    private static function getDurationProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'duration']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDateStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'duration']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralDateStatusPropertiesNodes as $lomGeneralDateStatusPropertiesNode) {
            // Return empty array if no copyright
            if (empty($lomGeneralDateStatusPropertiesNode)) {
                return $result;
            }

            $lomGeneralDateStatusPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            // $propertyKeyAppend = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'dateTime']/@value")[0];
            $lomGeneralDateStatusValuePropertyKey = $propertyCatalogKey;

            $lomGeneralDateStatusValuePropertiesNodeValue = $lomGeneralDateStatusPropertiesNode->xpath("f:dictionary/f:string[@value = 'duration']/
            following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralDateStatusValuePropertiesNodeValue)) {
                // Get Values from string
                $lomGeneralDateStatusValuePropertiesNodeValue[0] = substr($lomGeneralDateStatusValuePropertiesNodeValue[0], 1);
                $durationValues = preg_replace('/\B([A-Z])/', '$1_', $lomGeneralDateStatusValuePropertiesNodeValue[0]);
                $durationValueArray = explode('T', $durationValues);

                $durationDateValueArray = explode('_', $durationValueArray[0]);
                $durationTimeValueArray = explode('_', $durationValueArray[1]);

                // Get values from different entities name
                foreach ($durationDateValueArray as $entityArrayValue) {
                    foreach (self::DURATION_DATE_TAG_VALUES as $entityChildTagValue) {
                        if (str_contains($entityArrayValue, $entityChildTagValue)) {
                            // Get entity child array of tag name and value
                            // $entityChildArrayValues = preg_split('/([A-Z]?=)/',$entityChildTagValue);
                            if (isset($entityChildTagValue)) {
                                foreach (self::DURATION_DATE_TAG_VALUES as $entityChildKey => $value) {
                                    if (str_contains($entityArrayValue, $value)) {
                                        $durationValueLength = strlen($entityArrayValue) - 1;
                                        $durationValue = substr($entityArrayValue, 0, $durationValueLength);
                                        $entityChildKeyAppend = self::ODE_XML_KEY_UNDERSCORE.$entityChildKey;
                                        $lomGeneralContributionPropertyEntityChildKey = $lomGeneralDateStatusValuePropertyKey.$entityChildKeyAppend;
                                        $odeProperties = new OdePropertiesSync();
                                        $odeProperties->setOdeSessionId($odeSessionId);
                                        $odeProperties->setKey($lomGeneralContributionPropertyEntityChildKey);
                                        $odeProperties->setValue($durationValue);

                                        if (isset($odeProperties)) {
                                            array_push($result['odeProperties'], $odeProperties);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Get values from different entities name
                foreach ($durationTimeValueArray as $entityArrayValue) {
                    foreach (self::DURATION_TIME_TAG_VALUES as $entityChildTagValue) {
                        if (str_contains($entityArrayValue, $entityChildTagValue)) {
                            // Get entity child array of tag name and value
                            // $entityChildArrayValues = preg_split('/([A-Z]?=)/',$entityChildTagValue);
                            if (isset($entityChildTagValue)) {
                                foreach (self::DURATION_TIME_TAG_VALUES as $entityChildKey => $value) {
                                    if (str_contains($entityArrayValue, $value)) {
                                        $durationValueLength = strlen($entityArrayValue) - 1;
                                        $durationValue = substr($entityArrayValue, 0, $durationValueLength);
                                        $entityChildKeyAppend = self::ODE_XML_KEY_UNDERSCORE.$entityChildKey;
                                        $lomGeneralContributionPropertyEntityChildKey = $lomGeneralDateStatusValuePropertyKey.$entityChildKeyAppend;
                                        $odeProperties = new OdePropertiesSync();
                                        $odeProperties->setOdeSessionId($odeSessionId);
                                        $odeProperties->setKey($lomGeneralContributionPropertyEntityChildKey);
                                        $odeProperties->setValue($durationValue);

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
