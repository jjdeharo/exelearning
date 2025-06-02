<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesEducationalGet.
 */
class OdeOldXmlCatalogPropertiesEducationalGet
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

    public static function oldElpCatalogEducationalPropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $propertyKeyAppend = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'educational']/@value")[0];
        $propertyCatalogLifeCycleKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomLifeCyclePropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'educational']/
        following-sibling::f:list[1]/f:instance");
        foreach ($lomLifeCyclePropertiesNodes as $lomLifeCyclePropertiesNode) {
            $descriptionProperties = self::getDescriptionProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $languageProperties = self::getLanguageProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $difficultyProperties = self::getDifficultyProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $intendedEndUserRoleProperties = self::getIntendedEndUserRoleProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $interaciveLevelProperties = self::getInteractiveLevelProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $interaciveTypeProperties = self::getInteractiveTypeProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $learningResourceTypeProperties = self::getLearningResourceTypeProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $semanticDensityProperties = self::getSemanticDensityProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $typicalAgeRangeProperties = self::getTypicalAgeRangeProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $typicalLearningTimeProperties = self::getTypicalLearningTimeProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);
            $contextProperties = self::getContextProperties($odeSessionId, $lomLifeCyclePropertiesNode, $xpathNamespace, $propertyCatalogLifeCycleKey);

            $lomMetadataPropertiesArray = array_merge(
                $descriptionProperties['odeProperties'],
                $languageProperties['odeProperties'],
                $difficultyProperties['odeProperties'],
                $intendedEndUserRoleProperties['odeProperties'],
                $interaciveLevelProperties['odeProperties'],
                $interaciveTypeProperties['odeProperties'],
                $learningResourceTypeProperties['odeProperties'],
                $semanticDensityProperties['odeProperties'],
                $typicalAgeRangeProperties['odeProperties'],
                $typicalLearningTimeProperties['odeProperties'],
                $contextProperties['odeProperties'],
            );
            foreach ($lomMetadataPropertiesArray as $lomMetadataProperty) {
                array_push($result['odeProperties'], $lomMetadataProperty);
            }

            // $catalogSub = $lomEducationalSubProperty->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.catalogSub']");
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

    private static function getDifficultyProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'difficulty']/@value")[0];
        $propertyCatalogGeneralStructureKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralStructurePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'difficulty']/
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

    private static function getIntendedEndUserRoleProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'intendedEndUserRole']/@value")[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralIdentfierPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'intendedEndUserRole']/
        following-sibling::f:list[1]/f:instance");
        $i = 2;
        foreach ($lomGeneralIdentfierPropertiesNodes as $key => $lomGeneralIdentfierPropertiesNode) {
            // Return empty array if no identifier
            if (empty($lomGeneralIdentfierPropertiesNode)) {
                return $result;
            }
            $lomGeneralIdentfierPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $propertyKeyAppend = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='value']/@value")[0];
            $lomGeneralIdentfierPropertyCatalogKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $lomGeneralIdentfierPropertyCatalogNodes = $lomGeneralIdentfierPropertiesNode->xpath("f:dictionary/f:string[@value='value']/following-sibling::f:instance[1]");

            foreach ($lomGeneralIdentfierPropertyCatalogNodes as $lomGeneralIdentfierPropertyCatalogNode) {
                $lomGeneralIdentfierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                // $lomGeneralIdentfierPropertyCatalogKeyMaxVersion = $lomGeneralIdentfierPropertyCatalogKey . self::ODE_XML_KEY_UNDERSCORE . $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='minimumVersion']/@value")[0];
                $lomGeneralIdentfierPropertyCatalogNodeValue = $lomGeneralIdentfierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");

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
        }

        return $result;
    }

    private static function getInteractiveLevelProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'interactivityLevel']/@value")[0];
        $propertyCatalogGeneralStructureKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralStructurePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'interactivityLevel']/
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

    private static function getInteractiveTypeProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'interactivityType']/@value")[0];
        $propertyCatalogGeneralStructureKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralStructurePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'interactivityType']/
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

    private static function getLearningResourceTypeProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'learningResourceType']/@value")[0];
        $propertyCatalogGeneralStructureKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralStructurePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'learningResourceType']/
        following-sibling::f:list[1]/f:instance");
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

    private static function getSemanticDensityProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'semanticDensity']/@value")[0];
        $propertyCatalogGeneralStructureKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralStructurePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'semanticDensity']/
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

    private static function getTypicalAgeRangeProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'typicalAgeRange']/@value")[0];
        $propertyCatalogGeneralDescriptionKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDescriptionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'typicalAgeRange']/
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

    private static function getTypicalLearningTimeProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'typicalLearningTime']/@value");
        if (empty($propertyKeyAppend)) {
            return $result;
        } else {
            $propertyKeyAppend = $propertyKeyAppend[0];
        }

        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralDateStatusPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'typicalLearningTime']/
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

    private static function getContextProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $propertyKeyAppend = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'context']/@value")[0];
        $propertyCatalogGeneralStructureKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralStructurePropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'context']/
        following-sibling::f:list[1]/f:instance");
        $i = 2;
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
                if ($key >= '1') {
                    $lomGeneralStructurePropertyStringKey = $propertyCatalogGeneralStructureKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    ++$i;
                }
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
