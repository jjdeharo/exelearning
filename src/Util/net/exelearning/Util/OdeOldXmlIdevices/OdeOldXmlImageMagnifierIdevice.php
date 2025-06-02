<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlIdevices;

use App\Constants;
use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use App\Util\net\exelearning\Util\UrlUtil;
use App\Util\net\exelearning\Util\Util;

/**
 * OdeOldXmlImageMagnifierIdevice.
 */
class OdeOldXmlImageMagnifierIdevice
{
    // Old Xml idevice content
    public const OLD_ODE_XML_INSTANCE = 'instance';
    public const OLD_ODE_XML_DICTIONARY = 'dictionary';
    public const OLD_ODE_XML_LIST = 'list';
    public const OLD_ODE_XML_UNICODE = 'unicode';
    public const OLD_ODE_XML_ATTRIBUTES = '@attributes';
    // const OLD_ODE_XML_IDEVICE_TEXT = 'instance';
    public const OLD_ODE_XML_IDEVICE_TEXT_CONTENT = 'string role="key" value="content_w_resourcePaths"';
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

    public static function oldElpImageMagnifierIdeviceStructure($odeSessionId, $odePageId, $galleryImageNodes, $generatedIds, $xpathNamespace)
    {
        $result['odeComponentsSync'] = [];
        $result['srcRoutes'] = [];
        $jsonImages = [];

        foreach ($galleryImageNodes as $galleryImageNode) {
            // Count images to set in json
            $imageCount = 0;
            // Get Images
            $galleryImageNode->registerXPathNamespace('f', $xpathNamespace);
            $images = $galleryImageNode->xpath("f:dictionary/f:instance/f:dictionary/f:instance[@class='exe.engine.resource.Resource']");

            // Get blockName
            $blockNameNode = $galleryImageNode->xpath("f:dictionary/f:string[@value='caption']/following-sibling::f:unicode[1]/@value");

            $htmlContentNode = $galleryImageNode->xpath("f:dictionary/f:instance[@class='exe.engine.field.TextAreaField']/f:dictionary/
            f:string[@value='content_w_resourcePaths']/following-sibling::f:unicode[1]");

            $odeIdeviceId = Util::generateIdCheckUnique($generatedIds);
            $generatedIds[] = $odeIdeviceId;
            $odeComponentsMapping[] = $odeIdeviceId;

            foreach ($images as $image) {
                // Get Image  and thumbnail path
                $image->registerXPathNamespace('f', $xpathNamespace);
                $imagePath = $image->xpath("f:dictionary/f:string[@value='_storageName']/following-sibling::f:string[1]");

                $sessionPath = null;

                if (!empty($odeSessionId)) {
                    $sessionPath = UrlUtil::getOdeSessionUrl($odeSessionId);
                }

                $fullImagePath = $sessionPath.$odeIdeviceId.Constants::SLASH.(string) $imagePath[0]['value'];
                // $fullThumbnailPath = $sessionPath . $odeIdeviceId . Constants::SLASH . (string) $imagePath[1]["value"];

                // Common replaces for all OdeComponents
                $commonReplaces = [
                    '{{image_path}}' => $sessionPath.$odeIdeviceId.Constants::SLASH.(string) $imagePath[0]['value'],
                    // "{{thumb_path}}" => $sessionPath . $odeIdeviceId . Constants::SLASH . (string) $imagePath[1]["value"]
                ];

                array_push($jsonImages, $fullImagePath);

                array_push($result['srcRoutes'], $fullImagePath);
                // array_push($result["srcRoutes"], $fullThumbnailPath);

                ++$imageCount;
            }

            $subOdePagStructureSync = new OdePagStructureSync();
            $odeBlockId = Util::generateIdCheckUnique($generatedIds);
            $generatedIds[] = $odeBlockId;

            // OdePagStructureSync fields
            $subOdePagStructureSync->setOdeSessionId($odeSessionId);
            $subOdePagStructureSync->setOdePageId($odePageId);
            $subOdePagStructureSync->setOdeBlockId($odeBlockId);
            // $odePagStructureSync->setIconName($xmlOdePagStructure->{self::ODE_XML_TAG_FIELD_ICON_NAME});

            // $odeBlockTitle = $oldXmlListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}["value"][0];
            $subOdePagStructureSync->setBlockName($blockNameNode[0]);

            $orderPage = (string) $galleryImageNode['reference'];
            $subOdePagStructureSync->setOdePagStructureSyncOrder(intval($orderPage));

            // Get pagStructureSync properties
            $subOdePagStructureSync->loadOdePagStructureSyncPropertiesFromConfig();

            $odeComponentsSync = new OdeComponentsSync();

            // OdeComponentsSync fields
            $odeComponentsSync->setOdeSessionId($odeSessionId);
            $odeComponentsSync->setOdePageId($odePageId);
            $odeComponentsSync->setOdeBlockId($odeBlockId);
            $odeComponentsSync->setOdeIdeviceId($odeIdeviceId);

            // $odeComponentsSync->setJsonProperties($odeComponentsSyncJsonProperties);

            $odeComponentsSync->setOdeComponentsSyncOrder(intval(1));
            // Set type
            $odeComponentsSync->setOdeIdeviceTypeName('text');

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

            if (isset($commonReplaces)) {
                // $odeComponentsSyncHtmlView = self::applyReplaces($commonReplaces,
                // $externalUrlNode->{self::OLD_ODE_XML_DICTIONARY}->{self::OLD_ODE_XML_UNICODE}[6]["value"]);
            } else {
                // $odeComponentsSyncHtmlView = $externalUrlNode->{self::OLD_ODE_XML_DICTIONARY}->{self::OLD_ODE_XML_UNICODE}[6]["value"];
            }
            $odeComponentsSyncHtmlView = '';
            // $externalUrlDiv = self::SET_EXTERNAL_URL_DIV;

            // $htmlReplace = ["{{image_count}}" => $odeComponentsSyncHtmlView];
            // $externalUrlDiv = self::applyHtmlChange($htmlReplace, $externalUrlDiv);

            // $odeComponentsSync->setHtmlView($externalUrlDiv);

            $jsonProperties = self::JSON_PROPERTIES;
            $jsonProperties['ideviceId'] = $odeIdeviceId;
            foreach ($jsonImages as $key => $jsonImage) {
                $odeComponentsSyncHtmlView .= '<img src="'.$jsonImage.'"></img>';
            }
            $odeComponentsSyncHtmlView = $odeComponentsSyncHtmlView.(string) $htmlContentNode[0]['value'];

            $odeComponentsSync->setHtmlView($odeComponentsSyncHtmlView);

            $jsonProperties['textTextarea'] = $odeComponentsSyncHtmlView;
            // Create jsonProperties for idevice

            $jsonProperties = json_encode($jsonProperties);
            $odeComponentsSync->setJsonProperties($jsonProperties);

            // OdeComponentsSync property fields
            $odeComponentsSync->loadOdeComponentsSyncPropertiesFromConfig();

            $subOdePagStructureSync->addOdeComponentsSync($odeComponentsSync);

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

    /**
     * Applies the replaces passed as param.
     *
     * @param array  $replaces
     * @param string $text
     */
    private static function applyHtmlChange($replaces, $text)
    {
        $result = $text;

        foreach ($replaces as $search => $replace) {
            $result = str_replace($search, $replace, $result);
        }

        return $result;
    }
}
