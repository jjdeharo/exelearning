<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlIdevices;

use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use App\Util\net\exelearning\Util\Util;

/**
 * OdeOldXmlFreeTextIdevice.
 */
class OdeOldXmlJavaAppIdevice
{
    // Create jsonProperties for idevice
    public const JSON_PROPERTIES = [
        'ideviceId' => '',
        'textInfoDurationInput' => '',
        'textInfoParticipantsInput' => '',
        'textInfoDurationTextInput' => 'Duration:',
        'textInfoParticipantsTextInput' => 'Grouping:',
        'textTextarea' => '',
        'textFeedbackInput' => 'Show Feedback',
        'textFeedbackTextarea' => '',
    ];

    // Old Xml idevice content
    public const OLD_ODE_XML_INSTANCE = 'instance';
    public const OLD_ODE_XML_DICTIONARY = 'dictionary';
    public const OLD_ODE_XML_LIST = 'list';
    public const OLD_ODE_XML_UNICODE = 'unicode';
    public const OLD_ODE_XML_ATTRIBUTES = '@attributes';
    // const OLD_ODE_XML_IDEVICE_TEXT = 'instance';
    public const OLD_ODE_XML_IDEVICE_TEXT_CONTENT = 'string role="key" value="content_w_resourcePaths"';

    public static function oldElpJavaAppIdeviceStructure($odeSessionId, $odePageId, $freeTextNodes, $generatedIds, $xpathNamespace)
    {
        $result['odeComponentsSync'] = [];
        $result['srcRoutes'] = [];

        foreach ($freeTextNodes as $freeTextNode) {
            $freeTextNode->registerXPathNamespace('f', $xpathNamespace);

            // Get blockName
            $blockNameNode = $freeTextNode->xpath("f:dictionary/f:string[@value='_title']/following-sibling::f:unicode[1]/@value");

            $nodeIdevices = $freeTextNode->xpath('f:dictionary');
            foreach ($nodeIdevices as $nodeIdevice) {
                // IDEVICE TEXT CONTENT
                // if($nodeIdevice->{self::OLD_ODE_XML_DICTIONARY}->{self::OLD_ODE_XML_UNICODE}) {

                $subOdePagStructureSync = new OdePagStructureSync();
                $odeBlockId = Util::generateIdCheckUnique($generatedIds);
                $generatedIds[] = $odeBlockId;

                // OdePagStructureSync fields
                $subOdePagStructureSync->setOdeSessionId($odeSessionId);
                $subOdePagStructureSync->setOdePageId($odePageId);
                $subOdePagStructureSync->setOdeBlockId($odeBlockId);
                // $odePagStructureSync->setIconName($xmlOdePagStructure->{self::ODE_XML_TAG_FIELD_ICON_NAME});

                // $odeBlockTitle = $oldXmlListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}["value"][0];
                $subOdePagStructureSync->setBlockName((string) $blockNameNode[0]);

                $nodeIdevice->registerXPathNamespace('f', $xpathNamespace);
                $orderPageNode = $nodeIdevice->xpath("f:list/f:instance/f:dictionary/f:string[@value='_idevice']/
                    following-sibling::f:reference[1]/@key");
                if (!empty($orderPageNode)) {
                    $orderPage = $orderPageNode[0];
                    $subOdePagStructureSync->setOdePagStructureSyncOrder(intval($orderPage));
                } else {
                    $subOdePagStructureSync->setOdePagStructureSyncOrder(intval('1'));
                }
                // $orderPage = (string) $nodeIdevice["reference"];

                // Get pagStructureSync properties
                $subOdePagStructureSync->loadOdePagStructureSyncPropertiesFromConfig();
                // foreach($oldXmlListInstDict->{self::OLD_ODE_XML_UNICODE} as $oldXmlListInstDictUnicode){
                //     // array_push($odeResponse, $oldXmlListInstDictUnicode);
                //     if($oldXmlListInstDictUnicode["value"]) {
                //         $odePagStructureSync->setBlockName($oldXmlListInstDictUnicode["value"]);
                //     }

                // }

                $odeComponentsSync = new OdeComponentsSync();
                $odeIdeviceId = Util::generateIdCheckUnique($generatedIds);
                $generatedIds[] = $odeIdeviceId;
                $odeComponentsMapping[] = $odeIdeviceId;

                // OdeComponentsSync fields
                $odeComponentsSync->setOdeSessionId($odeSessionId);
                $odeComponentsSync->setOdePageId($odePageId);
                $odeComponentsSync->setOdeBlockId($odeBlockId);
                $odeComponentsSync->setOdeIdeviceId($odeIdeviceId);

                // $odeComponentsSync->setJsonProperties($odeComponentsSyncJsonProperties);

                $odeComponentsSync->setOdeComponentsSyncOrder(intval(1));
                // Set type
                $odeComponentsSync->setOdeIdeviceTypeName('text');

                // foreach($nodeIdevice->{self::OLD_ODE_XML_DICTIONARY} as $oldXmlListDictListInstDictListInstDict) {

                // $oldXmlListDictListInstDictListInstDict->registerXPathNamespace('f', $xpathNamespace);
                // $fileTextPathToChange = $oldXmlListDictListInstDictListInstDict->xpath('//f:unicode[@content="true"]starts-with(@src,"resources/")]');
                // src="resources/

                $odeComponentsSyncHtmlView = 'Java app idevice is no longer available';

                $odeComponentsSync->setHtmlView($odeComponentsSyncHtmlView);

                $jsonProperties = self::JSON_PROPERTIES;
                $jsonProperties['ideviceId'] = $odeIdeviceId;
                $jsonProperties['textTextarea'] = $odeComponentsSyncHtmlView;
                // Create jsonProperties for idevice

                $jsonProperties = json_encode($jsonProperties);
                $odeComponentsSync->setJsonProperties($jsonProperties);

                // OdeComponentsSync property fields
                $odeComponentsSync->loadOdeComponentsSyncPropertiesFromConfig();

                // $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]["value"];
                $subOdePagStructureSync->addOdeComponentsSync($odeComponentsSync);
                // }
                // }
                // }
                array_push($result['odeComponentsSync'], $subOdePagStructureSync);
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
