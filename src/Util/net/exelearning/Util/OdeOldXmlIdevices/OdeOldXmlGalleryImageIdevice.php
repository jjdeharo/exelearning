<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlIdevices;

use App\Constants;
use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use App\Util\net\exelearning\Util\UrlUtil;
use App\Util\net\exelearning\Util\Util;

/**
 * OdeOldXmlGalleryImageIdevice.
 */
class OdeOldXmlGalleryImageIdevice
{
    // Old Xml idevice content
    public const OLD_ODE_XML_INSTANCE = 'instance';
    public const OLD_ODE_XML_DICTIONARY = 'dictionary';
    public const OLD_ODE_XML_LIST = 'list';
    public const OLD_ODE_XML_UNICODE = 'unicode';
    public const OLD_ODE_XML_ATTRIBUTES = '@attributes';
    // const OLD_ODE_XML_IDEVICE_TEXT = 'instance';
    public const OLD_ODE_XML_IDEVICE_TEXT_CONTENT = 'string role="key" value="content_w_resourcePaths"';
    // json Structure
    public const JSON_STRUCTURE = [
        'ideviceId' => '',
    ];
    // Images structure to add in json structure
    public const IMAGES_JSON_STRUCTURE = [
        'img' => '{{image_path}}',
        'thumbnail' => '{{thumb_path}}',
        'title' => '',
        'linktitle' => '',
        'author' => '',
        'linkauthor' => '',
        'license' => '',
        // "{'img':'{{image_path}}', 'thumbnail':'{{thumb_path}}', 'title':'', 'linktitle':'', 'author':'', 'linkauthor':'', 'license':''}"
    ];

    public static function oldElpGalleryImageIdeviceStructure($odeSessionId, $odePageId, $galleryImageNodes, $generatedIds, $xpathNamespace)
    {
        $result['odeComponentsSync'] = [];
        $result['srcRoutes'] = [];
        $jsonImages = [];

        foreach ($galleryImageNodes as $galleryImageNode) {
            // Count images to set in json
            $imageCount = 0;
            // Get Images
            $galleryImageNode->registerXPathNamespace('f', $xpathNamespace);
            // Get blockName
            $blockNameNode = $galleryImageNode->xpath("f:dictionary/f:string[@value='_title']/following-sibling::f:unicode[1]/@value");

            $images = $galleryImageNode->xpath("f:dictionary/f:instance/f:dictionary/f:list/f:instance[@class='exe.engine.galleryidevice.GalleryImage']");

            $odeIdeviceId = Util::generateIdCheckUnique($generatedIds);
            $generatedIds[] = $odeIdeviceId;
            $odeComponentsMapping[] = $odeIdeviceId;

            foreach ($images as $image) {
                // Get Image  and thumbnail path
                $image->registerXPathNamespace('f', $xpathNamespace);
                $imagePath = $image->xpath("f:dictionary/f:instance[@class='exe.engine.resource.Resource']/f:dictionary/f:string[@value='_storageName']/following-sibling::f:string[1]");

                $sessionPath = null;

                if (!empty($odeSessionId)) {
                    $sessionPath = UrlUtil::getOdeSessionUrl($odeSessionId);
                }

                $fullImagePath = $sessionPath.$odeIdeviceId.Constants::SLASH.(string) $imagePath[0]['value'];
                $fullThumbnailPath = $sessionPath.$odeIdeviceId.Constants::SLASH.(string) $imagePath[1]['value'];

                // Common replaces for all OdeComponents
                $commonReplaces = [
                    '{{image_path}}' => $sessionPath.$odeIdeviceId.Constants::SLASH.(string) $imagePath[0]['value'],
                    '{{thumb_path}}' => $sessionPath.$odeIdeviceId.Constants::SLASH.(string) $imagePath[1]['value'],
                ];

                $jsonProperties = self::IMAGES_JSON_STRUCTURE;

                $jsonImg = self::applyHtmlChange($commonReplaces, $jsonProperties);
                array_push($jsonImages, $jsonImg);

                array_push($result['srcRoutes'], $fullImagePath);
                array_push($result['srcRoutes'], $fullThumbnailPath);

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
            $subOdePagStructureSync->setBlockName((string) $blockNameNode[0]);

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
            $odeComponentsSync->setOdeIdeviceTypeName('image-gallery');

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

            // $externalUrlDiv = self::SET_EXTERNAL_URL_DIV;

            // $htmlReplace = ["{{image_count}}" => $odeComponentsSyncHtmlView];
            // $externalUrlDiv = self::applyHtmlChange($htmlReplace, $externalUrlDiv);

            // $odeComponentsSync->setHtmlView($externalUrlDiv);

            $jsonProperties = self::JSON_STRUCTURE;
            $jsonProperties['ideviceId'] = $odeIdeviceId;
            foreach ($jsonImages as $key => $jsonImage) {
                $jsonProperties['img_'.$key] = $jsonImage;
            }

            // $jsonProperties["textTextarea"] = $odeComponentsSyncHtmlView;
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
