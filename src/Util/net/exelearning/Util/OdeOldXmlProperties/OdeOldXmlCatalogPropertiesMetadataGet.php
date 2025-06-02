<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlProperties;

use App\Entity\net\exelearning\Entity\OdePropertiesSync;

/**
 * OdeOldXmlCatalogPropertiesMetadataGet.
 */
class OdeOldXmlCatalogPropertiesMetadataGet
{
    // metadata entity values tag name
    public const ENTITY_METADATA_TAG_VALUES = [
        'organization' => 'ORG',
        'name' => 'FN',
        'email' => 'EMAIL;TYPE=INTERNET',
    ];

    // duration values tag name
    public const DURATION_DATE_TAG_VALUES = [
        'years' => 'Y',
        'months' => 'M',
        'days' => 'D',
    ];
    public const DURATION_TIME_TAG_VALUES = [
        'hours' => 'H',
        'minutes' => 'M',
        'seconds' => 'S',
    ];
    // metadata schema values tag name
    public const METADATA_SCHEMA_VALUES = [
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
    // OLD_ODE_XML_IDEVICE_TEXT_CONTENT holds the text used to find a specific instance
    public const OLD_ODE_XML_IDEVICE_TEXT_CONTENT = 'string role="key" value="content_w_resourcePaths"';

    /**
     * Retrieves metadata properties from the old elp catalog.
     *
     * @param string            $odeSessionId
     * @param \SimpleXMLElement $nodeCatalogProperties
     * @param string            $propertyCatalogKey
     * @param string            $xpathNamespace
     *
     * @return array
     */
    public static function oldElpCatalogMetadataPropertiesGet($odeSessionId, $nodeCatalogProperties, $propertyCatalogKey, $xpathNamespace)
    {
        $result = [];
        $result['odeProperties'] = [];

        $xpathResult = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'metaMetadata']/@value");
        if (!isset($xpathResult[0])) {
            // Return empty result if the expected node is missing
            return $result;
        }
        $propertyKeyAppend = $xpathResult[0];

        $propertyCatalogMetadataKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomMetadataPropertiesNodes = $nodeCatalogProperties->xpath("f:dictionary/f:string[@value = 'metaMetadata']/
        following-sibling::f:instance[@class='exe.engine.lom.lomsubs.metaMetadataSub']");
        if (!is_array($lomMetadataPropertiesNodes)) {
            return $result;
        }
        foreach ($lomMetadataPropertiesNodes as $lomMetadataPropertiesNode) {
            $identifierProperties = self::getIdentifierProperties($odeSessionId, $lomMetadataPropertiesNode, $xpathNamespace, $propertyCatalogMetadataKey);
            $languageProperties = self::getLanguageMetadataProperties($odeSessionId, $lomMetadataPropertiesNode, $xpathNamespace, $propertyCatalogMetadataKey);
            $contributionProperties = self::getContributionProperties($odeSessionId, $lomMetadataPropertiesNode, $xpathNamespace, $propertyCatalogMetadataKey);
            $metadataSchemaProperties = self::getMetadataSchemaProperties($odeSessionId, $lomMetadataPropertiesNode, $xpathNamespace, $propertyCatalogMetadataKey);

            $lomMetadataPropertiesArray = array_merge(
                $identifierProperties['odeProperties'],
                $languageProperties['odeProperties'],
                $contributionProperties['odeProperties'],
                $metadataSchemaProperties['odeProperties']
            );
            foreach ($lomMetadataPropertiesArray as $lomMetadataProperty) {
                array_push($result['odeProperties'], $lomMetadataProperty);
            }
        }

        return $result;
    }

    /**
     * Retrieves metadata schema properties.
     *
     * @param string            $odeSessionId
     * @param \SimpleXMLElement $lomGeneralPropertiesNode
     * @param string            $xpathNamespace
     * @param string            $propertyCatalogGeneralKey
     *
     * @return array
     */
    private static function getMetadataSchemaProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $schemaKeyArray = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'metadataSchema']/@value");
        if (!isset($schemaKeyArray[0])) {
            return $result;
        }
        $propertyKeyAppend = $schemaKeyArray[0];
        $propertyCatalogGeneralMetadataSchemaKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralMetadataSchemaPropertiesNodeValue = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'metadataSchema']/
        following-sibling::f:list[1]/f:instance/f:dictionary/f:string[@value = 'valueOf_']/following-sibling::f:string[1]/@value");

        if (!empty($lomGeneralMetadataSchemaPropertiesNodeValue) && isset($lomGeneralMetadataSchemaPropertiesNodeValue[0])) {
            foreach (self::METADATA_SCHEMA_VALUES as $metadataSchemaKey => $metadataSchemaValue) {
                if (str_contains($lomGeneralMetadataSchemaPropertiesNodeValue[0], $metadataSchemaValue)) {
                    $lomGeneralMetadataSchemaPropertiesNodeValue[0] = $metadataSchemaKey;
                }
            }
            $odeProperties = new OdePropertiesSync();
            $odeProperties->setOdeSessionId($odeSessionId);
            $odeProperties->setKey($propertyCatalogGeneralMetadataSchemaKey);
            $odeProperties->setValue($lomGeneralMetadataSchemaPropertiesNodeValue[0]);
            array_push($result['odeProperties'], $odeProperties);
        }

        return $result;
    }

    /**
     * Retrieves identifier properties.
     *
     * @param string            $odeSessionId
     * @param \SimpleXMLElement $lomGeneralPropertiesNode
     * @param string            $xpathNamespace
     * @param string            $propertyCatalogKey
     *
     * @return array
     */
    private static function getIdentifierProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $identifierKeyArray = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'identifier']/@value");
        if (!isset($identifierKeyArray[0])) {
            return $result;
        }
        $propertyKeyAppend = $identifierKeyArray[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $lomGeneralIdentifierPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'identifier']/
        following-sibling::f:list[1]");
        if (!is_array($lomGeneralIdentifierPropertiesNodes)) {
            return $result;
        }
        foreach ($lomGeneralIdentifierPropertiesNodes as $lomGeneralIdentifierPropertiesNode) {
            if (empty($lomGeneralIdentifierPropertiesNode)) {
                continue;
            }

            $lomGeneralIdentifierPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $catalogKeyArray = $lomGeneralIdentifierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='catalog']/@value");
            if (!isset($catalogKeyArray[0])) {
                continue;
            }
            $propertyKeyAppend = $catalogKeyArray[0];
            $identifierPropertyCatalogKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $identifierPropertyCatalogNodes = $lomGeneralIdentifierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='catalog']/following-sibling::f:instance[1]");
            if (is_array($identifierPropertyCatalogNodes)) {
                $i = 2;
                foreach ($identifierPropertyCatalogNodes as $key => $identifierPropertyCatalogNode) {
                    $identifierPropertyCatalogNode->registerXPathNamespace('f', $xpathNamespace);
                    $identifierValueArray = $identifierPropertyCatalogNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                        following-sibling::f:string[1]/@value");
                    if (!empty($identifierValueArray) && isset($identifierValueArray[0])) {
                        $odeProperties = new OdePropertiesSync();
                        $odeProperties->setOdeSessionId($odeSessionId);
                        if ($key >= 1) {
                            $identifierPropertyCatalogKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                            ++$i;
                        }
                        $odeProperties->setKey($identifierPropertyCatalogKey);
                        $odeProperties->setValue($identifierValueArray[0]);
                        array_push($result['odeProperties'], $odeProperties);
                    }
                }
            }
            $entryKeyArray = $lomGeneralIdentifierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='entry']/@value");
            if (!isset($entryKeyArray[0])) {
                continue;
            }
            $propertyKeyAppend = $entryKeyArray[0];
            $identifierPropertyEntryKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $identifierPropertyEntryNodes = $lomGeneralIdentifierPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.identifierSub']/
                f:dictionary/f:string[@value='entry']/following-sibling::f:instance[1]");
            if (is_array($identifierPropertyEntryNodes)) {
                $i = 2;
                foreach ($identifierPropertyEntryNodes as $key => $identifierPropertyEntryNode) {
                    $identifierPropertyEntryNode->registerXPathNamespace('f', $xpathNamespace);
                    $entryValueArray = $identifierPropertyEntryNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                        following-sibling::f:string[1]/@value");
                    if (!empty($entryValueArray) && isset($entryValueArray[0])) {
                        $odeProperties = new OdePropertiesSync();
                        $odeProperties->setOdeSessionId($odeSessionId);
                        if ($key >= 1) {
                            $identifierPropertyEntryKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                            ++$i;
                        }
                        $odeProperties->setKey($identifierPropertyEntryKey);
                        $odeProperties->setValue($entryValueArray[0]);
                        array_push($result['odeProperties'], $odeProperties);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Retrieves language metadata properties.
     *
     * @param string            $odeSessionId
     * @param \SimpleXMLElement $lomGeneralPropertiesNode
     * @param string            $xpathNamespace
     * @param string            $propertyCatalogGeneralKey
     *
     * @return array
     */
    private static function getLanguageMetadataProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogGeneralKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $languageKeyArray = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'language']/@value");
        if (!isset($languageKeyArray[0])) {
            return $result;
        }
        $propertyKeyAppend = $languageKeyArray[0];
        $propertyCatalogGeneralLanguageKey = $propertyCatalogGeneralKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $languageValueArray = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'language']/
        following-sibling::f:instance[1]/f:dictionary/f:string[@value = 'valueOf_']/following-sibling::f:string[1]/@value");
        if (!empty($languageValueArray) && isset($languageValueArray[0])) {
            $odeProperties = new OdePropertiesSync();
            $odeProperties->setOdeSessionId($odeSessionId);
            $odeProperties->setKey($propertyCatalogGeneralLanguageKey);
            $odeProperties->setValue($languageValueArray[0]);
            array_push($result['odeProperties'], $odeProperties);
        }

        return $result;
    }

    /**
     * Retrieves contribution properties.
     *
     * @param string            $odeSessionId
     * @param \SimpleXMLElement $lomGeneralPropertiesNode
     * @param string            $xpathNamespace
     * @param string            $propertyCatalogKey
     *
     * @return array
     */
    private static function getContributionProperties($odeSessionId, $lomGeneralPropertiesNode, $xpathNamespace, $propertyCatalogKey)
    {
        $result = [];
        $result['odeProperties'] = [];

        $lomGeneralPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
        $contributeKeyArray = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'contribute']/@value");
        if (!isset($contributeKeyArray[0])) {
            return $result;
        }
        $propertyKeyAppend = $contributeKeyArray[0];
        $propertyCatalogKey .= self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
        $contributionPropertiesNodes = $lomGeneralPropertiesNode->xpath("f:dictionary/f:string[@value = 'contribute']/
        following-sibling::f:list[1]");
        if (!is_array($contributionPropertiesNodes)) {
            return $result;
        }
        foreach ($contributionPropertiesNodes as $lomGeneralContributionPropertiesNode) {
            $lomGeneralContributionPropertiesNode->registerXPathNamespace('f', $xpathNamespace);
            $dateKeyArray = $lomGeneralContributionPropertiesNode->xpath("f:instance/
                f:dictionary/f:string[@value='date']/@value");
            if (empty($dateKeyArray) || !isset($dateKeyArray[0])) {
                continue;
            }
            $propertyKeyAppend = $dateKeyArray[0];
            $contributionPropertyDateKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $contributionDateNodes = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeMetaSub']/
                f:dictionary/f:string[@value='date']/following-sibling::f:instance[1]");
            if (is_array($contributionDateNodes)) {
                $i = 2;
                foreach ($contributionDateNodes as $key => $contributionDateNode) {
                    $contributionDateNode->registerXPathNamespace('f', $xpathNamespace);
                    $dateTimeValueArray = $contributionDateNode->xpath("f:dictionary/f:string[@value='dateTime']/
                        following-sibling::f:instance[1]/f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");
                    if (!empty($dateTimeValueArray) && isset($dateTimeValueArray[0])) {
                        $dateValueArray = explode('T', $dateTimeValueArray[0]);
                        $odeProperties = new OdePropertiesSync();
                        $odeProperties->setOdeSessionId($odeSessionId);
                        if ($key >= 1) {
                            $contributionPropertyDateKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                            ++$i;
                        }
                        $odeProperties->setKey($contributionPropertyDateKey);
                        $odeProperties->setValue($dateValueArray[0]);
                        array_push($result['odeProperties'], $odeProperties);
                    }
                }
            }
            $entityKeyArray = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeMetaSub']/
                f:dictionary/f:string[@value='entity']/@value");
            if (!isset($entityKeyArray[0])) {
                continue;
            }
            $propertyKeyAppend = $entityKeyArray[0];
            $contributionPropertyEntityKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
            $contributionEntityNodes = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeMetaSub']/
                f:dictionary/f:string[@value='entity']/following-sibling::f:list[1]/f:instance");
            if (is_array($contributionEntityNodes)) {
                $i = 2;
                foreach ($contributionEntityNodes as $key => $contributionEntityNode) {
                    $contributionEntityNode->registerXPathNamespace('f', $xpathNamespace);
                    $entityValueArray = $contributionEntityNode->xpath("f:dictionary/f:string[@value='valueOf_']/
                        following-sibling::f:string[1]/@value");
                    if (!empty($entityValueArray) && isset($entityValueArray[0])) {
                        $entityArrayValues = explode(' ', $entityValueArray[0]);
                        foreach ($entityArrayValues as $entityArrayValue) {
                            foreach (self::ENTITY_METADATA_TAG_VALUES as $entityChildKey => $entityChildTagValue) {
                                if (str_contains($entityArrayValue, $entityChildTagValue)) {
                                    $entityChildArrayValues = explode(':', $entityArrayValue);
                                    if (isset($entityChildArrayValues[1])) {
                                        $entityChildKeyAppend = self::ODE_XML_KEY_UNDERSCORE.$entityChildKey;
                                        $contributionPropertyEntityChildKey = $contributionPropertyEntityKey.$entityChildKeyAppend;
                                        $odeProperties = new OdePropertiesSync();
                                        $odeProperties->setOdeSessionId($odeSessionId);
                                        if ($key >= 1) {
                                            $contributionPropertyEntityChildKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend.$entityChildKeyAppend;
                                        }
                                        $odeProperties->setKey($contributionPropertyEntityChildKey);
                                        $odeProperties->setValue($entityChildArrayValues[1]);
                                        array_push($result['odeProperties'], $odeProperties);
                                    }
                                }
                            }
                        }
                        if ($key >= 1) {
                            ++$i;
                        }
                    }
                }
            }
            $roleKeyArray = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeMetaSub']/
                f:dictionary/f:string[@value='role']/@value");
            if (!isset($roleKeyArray[0])) {
                continue;
            }
            $propertyRoleKeyAppend = $roleKeyArray[0];
            $contributionPropertyRoleKey = $propertyCatalogKey.self::ODE_XML_KEY_UNDERSCORE.$propertyRoleKeyAppend;
            $contributionRoleNodes = $lomGeneralContributionPropertiesNode->xpath("f:instance[@class='exe.engine.lom.lomsubs.contributeMetaSub']/
                f:dictionary/f:string[@value='role']/following-sibling::f:instance");
            if (is_array($contributionRoleNodes)) {
                $i = 2;
                foreach ($contributionRoleNodes as $key => $contributionRoleNode) {
                    $contributionRoleNode->registerXPathNamespace('f', $xpathNamespace);
                    $roleValueKeyArray = $contributionRoleNode->xpath("f:dictionary/f:string[@value='value']/@value");
                    if (!isset($roleValueKeyArray[0])) {
                        continue;
                    }
                    $propertyKeyAppend = $roleValueKeyArray[0];
                    $contributionPropertyRoleValueKey = $contributionPropertyRoleKey.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                    $roleValueArray = $contributionRoleNode->xpath("f:dictionary/f:instance[@class='exe.engine.lom.lomsubs.roleValueSub']/
                        f:dictionary/f:string[@value='valueOf_']/following-sibling::f:string[1]/@value");
                    if (!empty($roleValueArray) && isset($roleValueArray[0])) {
                        $odeProperties = new OdePropertiesSync();
                        $odeProperties->setOdeSessionId($odeSessionId);
                        if ($key >= 1) {
                            $contributionPropertyRoleValueKey = $propertyCatalogKey.$i.self::ODE_XML_KEY_UNDERSCORE.$propertyRoleKeyAppend.self::ODE_XML_KEY_UNDERSCORE.$propertyKeyAppend;
                            ++$i;
                        }
                        $odeProperties->setKey($contributionPropertyRoleValueKey);
                        $odeProperties->setValue($roleValueArray[0]);
                        array_push($result['odeProperties'], $odeProperties);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Applies replacements to the given text.
     *
     * @param array  $replaces
     * @param string $text
     *
     * @return string
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
