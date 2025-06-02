<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesGeneralGet.
 */
class OdeOldXmlCatalogPropertiesGeneralGet
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

    public static function oldElpCatalogGeneralPropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $nodeCatalogProperties->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'general']/@value")[0];
        $propertyCatalogGeneralKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralPropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'general']/
        following-sibling::f:instance[@class='exe.engine.lom.lomsubs.generalSub']");
        foreach ($lomGeneralPropertiesNodes as $lomGeneralPropertiesNode) {
            $identifierProperties = self::getIdentifierProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);
            $titleProperties = self::getTitleProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);
            $languageProperties = self::getLanguageProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);
            $descriptionProperties = self::getDescriptionProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);
            $keywordProperties = self::getKeywordProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);
            $coverageProperties = self::getCoverageProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);
            $structureProperties = self::getStructureProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);
            $aggregationLevelProperties = self::getAggregationLevelProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey);

            $result['odeProperties'] = array_merge(
                $identifierProperties['odeProperties'],
                $titleProperties['odeProperties'],
                $languageProperties['odeProperties'],
                $descriptionProperties['odeProperties'],
                $keywordProperties['odeProperties'],
                $coverageProperties['odeProperties'],
                $structureProperties['odeProperties'],
                $aggregationLevelProperties['odeProperties'],
            );
            // $catalogSub = $lomEducationalSubProperty->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.catalogSub']");
        }

        return $result;
    }

    private static function getIdentifierProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'identifier']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralIdentfierPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'identifier']/
        following-sibling::f:list[1]");
        foreach ($lomGeneralIdentfierPropertiesNodes as $lomGeneralIdentfierPropertiesNode) {
            // Return empty array if no identifier
            if (empty($lomGeneralIdentfierPropertiesNode)) {
                return $result;
            }

            $lomGeneralIdentfierPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='catalog']/@value")[0];
            $lomGeneralIdentfierPropertyCatalogKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='catalog']/following-sibling::f:instance[1]");
            foreach ($lomGeneralIdentfierPropertyCatalogNodes as $key => $lomGeneralIdentfierPropertyCatalogNode) {
                $i = 2;
                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralIdentfierPropertyCatalogNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralIdentfierPropertyCatalogKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralIdentfierPropertyCatalogKey);
                    $odeProperties->setValue($lomGeneralIdentfierPropertyCatalogNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='entry']/@value")[0];
            $lomGeneralIdentfierPropertyEntryKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyEntryNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='entry']/following-sibling::f:instance[1]");
            foreach ($lomGeneralIdentfierPropertyEntryNodes as $key => $lomGeneralIdentfierPropertyEntryNode) {
                $i = '2';
                $lomGeneralIdentfierPropertyEntryNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralIdentfierPropertyEntryNodeValue = $lomGeneralIdentfierPropertyEntryNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralIdentfierPropertyEntryNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralIdentfierPropertyEntryKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralIdentfierPropertyEntryKey);
                    $odeProperties->setValue($lomGeneralIdentfierPropertyEntryNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getTitleProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'title']/@value")[0];
        $propertyCatalogGeneralTitleKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralTitlePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'title']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralTitlePropertiesNodes as $lomGeneralTitlePropertiesNode) {
            $lomGeneralTitlePropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralTitlePropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value")[0];
            $lomGeneralTitlePropertyStringKey = $propertyCatalogGeneralTitleKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralTitlePropertyStringNodes = $lomGeneralTitlePropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            $i = 2;
            foreach ($lomGeneralTitlePropertyStringNodes as $key => $lomGeneralTitlePropertyStringNode) {
                $lomGeneralTitlePropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralTitlePropertyStringNodeValue = $lomGeneralTitlePropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitlePropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralTitlePropertyStringKey = $propertyCatalogGeneralTitleKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralTitlePropertyStringKey);
                    $odeProperties->setValue($lomGeneralTitlePropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralTitleStringPropertyLanguageAppendKey = $lomGeneralTitlePropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralTitlePropertyLanguageKey = $propertyCatalogGeneralTitleKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralTitleStringPropertyLanguageAppendKey;
                $lomGeneralTitleStringPropertyLanguageNodeValue = $lomGeneralTitlePropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitleStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralTitlePropertyLanguageKey = $propertyCatalogGeneralTitleKey.$i.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralTitleStringPropertyLanguageAppendKey;
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralTitlePropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralTitleStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getLanguageProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'language']/@value")[0];
        $lomGeneralLanguagePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'language']/
        following-sibling::f:list[1]/f:instance");
        $i = 2;
        foreach ($lomGeneralLanguagePropertiesNodes as $key => $lomGeneralLanguagePropertiesNode) {
            $propertyCatalogGeneralLanguageKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;

            $lomGeneralLanguagePropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $lomGeneralLanguagePropertiesNodeValue = $lomGeneralLanguagePropertiesNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralLanguagePropertiesNodeValue)) {
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                if ($key >= '1') {
                    $propertyCatalogGeneralLanguageKey = $propertyCatalogGeneralLanguageKey.$i;
                    ++$i;
                }
                $odeProperties->setKey($propertyCatalogGeneralLanguageKey);
                $odeProperties->setValue($lomGeneralLanguagePropertiesNodeValue[0]);

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getDescriptionProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'description']/@value")[0];
        $propertyCatalogGeneralDescriptionKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDescriptionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'description']/
        following-sibling::f:list[1]/f:instance");
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

    private static function getKeywordProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'keyword']/@value")[0];
        $propertyCatalogGeneralKeywordKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralKeywordPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'keyword']/
        following-sibling::f:list[1]/f:instance");
        $i = 2;
        foreach ($lomGeneralKeywordPropertiesNodes as $key => $lomGeneralKeywordPropertiesNode) {
            $lomGeneralKeywordPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralKeywordPropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value")[0];
            $lomGeneralKeywordPropertyStringKey = $propertyCatalogGeneralKeywordKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralKeywordPropertyStringNodes = $lomGeneralKeywordPropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            foreach ($lomGeneralKeywordPropertyStringNodes as $lomGeneralKeywordPropertyStringNode) {
                $lomGeneralKeywordPropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralTitlePropertyStringNodeValue = $lomGeneralKeywordPropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitlePropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralKeywordPropertyStringKey = $propertyCatalogGeneralKeywordKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralKeywordPropertyStringKey);
                    $odeProperties->setValue($lomGeneralTitlePropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralTitleStringPropertyLanguageAppendKey = $lomGeneralKeywordPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralKeywordPropertyLanguageKey = $lomGeneralKeywordPropertyStringKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralTitleStringPropertyLanguageAppendKey;
                $lomGeneralTitleStringPropertyLanguageNodeValue = $lomGeneralKeywordPropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitleStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralKeywordPropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralTitleStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getCoverageProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'coverage']/@value")[0];
        $propertyCatalogGeneralCoverageKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralCoveragePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'coverage']/
        following-sibling::f:list[1]/f:instance");
        $i = 2;
        foreach ($lomGeneralCoveragePropertiesNodes as $key => $lomGeneralCoveragePropertiesNode) {
            $lomGeneralCoveragePropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralCoveragePropertiesNode->xpath("f:dictionary/f:string[@value='string']/@value")[0];
            $lomGeneralCoveragePropertyStringKey = $propertyCatalogGeneralCoverageKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralCoveragePropertyStringNodes = $lomGeneralCoveragePropertiesNode->xpath("f:dictionary/f:string[@value='string']
            /following-sibling::f:list[1]/f:instance");
            foreach ($lomGeneralCoveragePropertyStringNodes as $lomGeneralCoveragePropertyStringNode) {
                $lomGeneralCoveragePropertyStringNode->registerXPathNamespace('f', $xpathNamespace);
                $lomGeneralTitlePropertyStringNodeValue = $lomGeneralCoveragePropertyStringNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitlePropertyStringNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        $lomGeneralCoveragePropertyStringKey = $propertyCatalogGeneralCoverageKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    }
                    $odeProperties->setKey($lomGeneralCoveragePropertyStringKey);
                    $odeProperties->setValue($lomGeneralTitlePropertyStringNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }

                // Get language
                $lomGeneralTitleStringPropertyLanguageAppendKey = $lomGeneralCoveragePropertyStringNode->xpath("f:dictionary/f:string[@value='language']/@value")[0];
                $lomGeneralCoveragePropertyLanguageKey = $lomGeneralCoveragePropertyStringKey.self::ODE_XML_KEY_UNDERSCORE.$lomGeneralTitleStringPropertyLanguageAppendKey;
                $lomGeneralTitleStringPropertyLanguageNodeValue = $lomGeneralCoveragePropertyStringNode->xpath("f:dictionary/f:string[@value='language']/
                    following-sibling::f:string[1]/@value");

                if (!empty($lomGeneralTitleStringPropertyLanguageNodeValue)) {
                    $odeProperties = new OdePropertiesSync();
                    $odeProperties->setOdeSessionId($odeSessionId);
                    if ($key >= '1') {
                        ++$i;
                    }
                    $odeProperties->setKey($lomGeneralCoveragePropertyLanguageKey);
                    $odeProperties->setValue($lomGeneralTitleStringPropertyLanguageNodeValue[0]);
                }

                if (isset($odeProperties)) {
                    array_push($result['odeProperties'], $odeProperties);
                }
            }
        }

        return $result;
    }

    private static function getStructureProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'structure']/@value")[0];
        $propertyCatalogGeneralStructureKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralStructurePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'structure']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralStructurePropertiesNodes as $key => $lomGeneralStructurePropertiesNode) {
            // Return empty array if no generalProperties
            if (empty($lomGeneralStructurePropertiesNode)) {
                return $result;
            }
            $lomGeneralStructurePropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralStructurePropertiesNode->xpath("f:dictionary/f:string[@value='value']/@value");
            if (empty($propertyKeyAppend)) {
                return $result;
            }
            $propertyKeyAppend = $propertyKeyAppend[0];
            $lomGeneralStructurePropertyStringKey = $propertyCatalogGeneralStructureKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralStructurePropertyStringNodeValue = $lomGeneralStructurePropertiesNode->xpath("f:dictionary/f:string[@value='value']
            /following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralStructurePropertyStringNodeValue)) {
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                $odeProperties->setKey($lomGeneralStructurePropertyStringKey);
                $odeProperties->setValue($lomGeneralStructurePropertyStringNodeValue[0]);
            }

            if (isset($odeProperties)) {
                array_push($result['odeProperties'], $odeProperties);
            }
        }

        return $result;
    }

    private static function getAggregationLevelProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'aggregationLevel']/@value")[0];
        $propertyCatalogGeneralAggregationLevelKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralAggregationLevelPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'aggregationLevel']/
        following-sibling::f:instance[1]");
        foreach ($lomGeneralAggregationLevelPropertiesNodes as $key => $lomGeneralAggregationLevelPropertiesNode) {
            $lomGeneralAggregationLevelPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralAggregationLevelPropertiesNode->xpath("f:dictionary/f:string[@value='value']/@value")[0];
            $lomGeneralAggregationLevelPropertyStringKey = $propertyCatalogGeneralAggregationLevelKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralAggregationLevelPropertyStringNodeValue = $lomGeneralAggregationLevelPropertiesNode->xpath("f:dictionary/f:string[@value='value']
            /following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

            if (!empty($lomGeneralAggregationLevelPropertyStringNodeValue)) {
                $odeProperties = new OdePropertiesSync();
                $odeProperties->setOdeSessionId($odeSessionId);
                $odeProperties->setKey($lomGeneralAggregationLevelPropertyStringKey);
                $odeProperties->setValue($lomGeneralAggregationLevelPropertyStringNodeValue[0]);
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
