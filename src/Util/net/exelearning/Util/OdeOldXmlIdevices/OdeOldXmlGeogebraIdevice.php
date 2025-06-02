<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlIdevices;

use App\Constants;
use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use App\Util\net\exelearning\Util\UrlUtil;
use App\Util\net\exelearning\Util\Util;

/**
 * OdeOldXmlGeogebraIdevice.
 */
class OdeOldXmlGeogebraIdevice
{
    // Old Xml idevice content
    public const OLD_ODE_XML_INSTANCE = 'instance';
    public const OLD_ODE_XML_DICTIONARY = 'dictionary';
    public const OLD_ODE_XML_LIST = 'list';
    public const OLD_ODE_XML_UNICODE = 'unicode';
    public const OLD_ODE_XML_ATTRIBUTES = '@attributes';
    // const OLD_ODE_XML_IDEVICE_TEXT = 'instance';
    public const OLD_ODE_XML_IDEVICE_TEXT_CONTENT = 'string role="key" value="content_w_resourcePaths"';

    public static function oldElpGeogebraIdeviceStructure($odeSessionId, $odePageId, $geogebraNode, $generatedIds, $xpathNamespace)
    {
        $result['odeComponentsSync'] = [];
        $result['srcRoutes'] = [];

        $geogebraNode->registerXPathNamespace('f', $xpathNamespace);
        // Get blockName
        $blockNameNode = $geogebraNode->xpath("f:dictionary/f:string[@value='_title']/following-sibling::f:unicode[1]/@value");

        $nodeIdevices = $geogebraNode->xpath("f:dictionary/f:list/f:instance[@class='exe.engine.field.TextAreaField']");
        foreach ($nodeIdevices as $nodeIdevice) {
            // IDEVICE TEXT CONTENT
            if ($nodeIdevice->{self::OLD_ODE_XML_DICTIONARY}->{self::OLD_ODE_XML_UNICODE}) {
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

                $orderPage = (string) $nodeIdevice['reference'];
                $subOdePagStructureSync->setOdePagStructureSyncOrder(intval($orderPage));

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
                $odeComponentsSync->setOdeIdeviceTypeName('geogebra-activity');

                foreach ($nodeIdevice->{self::OLD_ODE_XML_DICTIONARY} as $oldXmlListDictListInstDictListInstDict) {
                    // $oldXmlListDictListInstDictListInstDict->registerXPathNamespace('f', $xpathNamespace);
                    // $fileTextPathToChange = $oldXmlListDictListInstDictListInstDict->xpath('//f:unicode[@content="true"]starts-with(@src,"resources/")]');
                    // src="resources/

                    $sessionPath = null;

                    if (!empty($odeSessionId)) {
                        $sessionPath = UrlUtil::getOdeSessionUrl($odeSessionId);
                    }

                    // Common replaces for all OdeComponents
                    $commonReplaces = [
                        'resources'.Constants::SLASH => $sessionPath.$odeIdeviceId.Constants::SLASH,
                    ];

                    if (
                        isset($oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]['content'])
                        && $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]['content'] == 'true'
                    ) {
                        if (isset($commonReplaces)) {
                            $odeComponentsSyncHtmlView = self::applyReplaces(
                                $commonReplaces,
                                $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]['value']
                            );
                        } else {
                            $odeComponentsSyncHtmlView = $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]['value'];
                        }

                        $prologue = '<?xml encoding="UTF-8">';
                        $html = $prologue.$odeComponentsSyncHtmlView;
                        $doc = new \DOMDocument();
                        @$doc->loadHTML($html);
                        $xpath = new \DOMXPath($doc);
                        $src = $xpath->evaluate('//img/@src', $doc); // "/images/image.jpg"
                        foreach ($src as $srcValue) {
                            $srcString = (string) $srcValue->value;
                            array_push($result['srcRoutes'], $srcString);
                        }

                        $odeComponentsSync->setHtmlView($odeComponentsSyncHtmlView);

                        // OdeComponentsSync property fields
                        $odeComponentsSync->loadOdeComponentsSyncPropertiesFromConfig();

                        // $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]["value"];
                        $subOdePagStructureSync->addOdeComponentsSync($odeComponentsSync);
                    }
                }
            }
            array_push($result['odeComponentsSync'], $subOdePagStructureSync);
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
