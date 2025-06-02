<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlIdevices;

use App\Constants;
use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use App\Util\net\exelearning\Util\UrlUtil;
use App\Util\net\exelearning\Util\Util;

/**
 * OdeOldXmlFpdSolvedExerciseIdevice.
 */
class OdeOldXmlFpdSolvedExerciseIdevice
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

    public static function oldElpFpdSolvedExerciseIdeviceStructure($odeSessionId, $odePageId, $freeTextNodes, $generatedIds, $xpathNamespace)
    {
        $result['odeComponentsSync'] = [];
        $result['srcRoutes'] = [];

        foreach ($freeTextNodes as $freeTextNode) {
            $freeTextNode->registerXPathNamespace('f', $xpathNamespace);
            // Get blockName
            $blockNameNode = $freeTextNode->xpath("f:dictionary/f:string[@value='_title']/following-sibling::f:unicode[1]/@value");

            $nodeIdevices = $freeTextNode->xpath("f:dictionary/f:string[@value='questions']/following-sibling::f:list[1]/f:instance[@class='exe.engine.ejercicioresueltofpdidevice.Question']");
            $nodeIdeviceStory = $freeTextNode->xpath("f:dictionary/f:string[@value='storyTextArea']/following-sibling::f:instance[1]");
            if (!empty($nodeIdeviceStory)) {
                $nodeIdeviceStory[0]->registerXPathNamespace('f', $xpathNamespace);
                $nodeIdeviceStoryText = $nodeIdeviceStory[0]->xpath("f:dictionary/f:string[@value='content_w_resourcePaths']/
                following-sibling::f:unicode[1]");
            }

            // Get first value of feedback node
            if (!empty($nodeFeedbackIdevice)) {
                $nodeFeedbackIdevice = $nodeFeedbackIdevice[0];
            }

            if (!empty($nodeIdevices)) {
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

                // Get pagStructureSync properties
                $subOdePagStructureSync->loadOdePagStructureSyncPropertiesFromConfig();

                // foreach($oldXmlListInstDict->{self::OLD_ODE_XML_UNICODE} as $oldXmlListInstDictUnicode){
                //     // array_push($odeResponse, $oldXmlListInstDictUnicode);
                //     if($oldXmlListInstDictUnicode["value"]) {
                //         $odePagStructureSync->setBlockName($oldXmlListInstDictUnicode["value"]);
                //     }

                // }

                // IDEVICE TEXT CONTENT
                if (isset($nodeIdeviceStoryText)) {
                    if (!empty($nodeIdeviceStoryText)) {
                        $contentHtmlText = $nodeIdeviceStoryText[0];
                    }

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

                    if (!empty($contentHtmlText)) {
                        if (isset($commonReplaces)) {
                            $odeComponentsSyncHtmlView = self::applyReplaces(
                                $commonReplaces,
                                $contentHtmlText[0]['value']
                            );
                        } else {
                            $contentHtmlText[0]['value'];
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

                        // Create json properties

                        $jsonProperties = self::JSON_PROPERTIES;
                        $jsonProperties['ideviceId'] = $odeIdeviceId;
                        $jsonProperties['textTextarea'] = $odeComponentsSyncHtmlView;

                        // Create jsonProperties for idevice
                        $jsonProperties = json_encode($jsonProperties);
                        $odeComponentsSync->setJsonProperties($jsonProperties);

                        // $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]["value"];
                        $subOdePagStructureSync->addOdeComponentsSync($odeComponentsSync);
                    }
                    array_push($result['odeComponentsSync'], $subOdePagStructureSync);
                }
                foreach ($nodeIdevices as $nodeIdevice) {
                    $nodeIdevice->registerXPathNamespace('f', $xpathNamespace);
                    $nodeIdeviceHtmlText = $nodeIdevice->xpath("f:dictionary/f:string[@value='questionTextArea']/
                    following-sibling::f:instance[1]");
                    if (!empty($nodeIdeviceHtmlText)) {
                        $nodeIdeviceHtmlText = $nodeIdeviceHtmlText[0];
                        $nodeIdeviceHtmlText->registerXPathNamespace('f', $xpathNamespace);
                        $contentHtmlText = $nodeIdeviceHtmlText->xpath("f:dictionary/f:string[@value='content_w_resourcePaths']/
                        following-sibling::f:unicode[1]");
                    }

                    $orderPage = (string) $nodeIdeviceHtmlText['reference'];
                    $subOdePagStructureSync->setOdePagStructureSyncOrder(intval($orderPage));

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

                    if (!empty($contentHtmlText)) {
                        if (isset($commonReplaces)) {
                            $odeComponentsSyncHtmlView = self::applyReplaces(
                                $commonReplaces,
                                $contentHtmlText[0]['value']
                            );
                        } else {
                            $contentHtmlText[0]['value'];
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

                        // Create json properties

                        $jsonProperties = self::JSON_PROPERTIES;
                        $jsonProperties['ideviceId'] = $odeIdeviceId;
                        $jsonProperties['textTextarea'] = $odeComponentsSyncHtmlView;

                        // Get the feedback from idevice (only one)
                        $nodeFeedbackIdevice = $nodeIdevice->xpath('f:dictionary/f:string[@value="feedbackTextArea"]/following-sibling::f:instance[1]');
                        if (!empty($nodeFeedbackIdevice)) {
                            $nodeFeedbackIdevice[0]->registerXPathNamespace('f', $xpathNamespace);
                            $nodeHtmltextFeedback = $nodeFeedbackIdevice[0]->xpath('f:dictionary/f:string[@value="content_w_resourcePaths"]/
                                    following-sibling::f:unicode[1]/@value')[0];
                            $sessionPath = null;

                            if (!empty($odeSessionId)) {
                                $sessionPath = UrlUtil::getOdeSessionUrl($odeSessionId);
                            }

                            // Common replaces for all OdeComponents
                            $commonReplaces = [
                                'resources'.Constants::SLASH => $sessionPath.$odeIdeviceId.Constants::SLASH,
                            ];

                            if (isset($commonReplaces)) {
                                $odeComponentsSyncFeedbackHtmlView = self::applyReplaces(
                                    $commonReplaces,
                                    (string) $nodeHtmltextFeedback
                                );
                            } else {
                                $odeComponentsSyncFeedbackHtmlView = (string) $nodeHtmltextFeedback;
                            }

                            $prologue = '<?xml encoding="UTF-8">';
                            $html = $prologue.$odeComponentsSyncFeedbackHtmlView;
                            $doc = new \DOMDocument();
                            @$doc->loadHTML($html);
                            $xpath = new \DOMXPath($doc);
                            $src = $xpath->evaluate('//img/@src', $doc); // "/images/image.jpg"
                            foreach ($src as $srcValue) {
                                $srcString = (string) $srcValue->value;
                                array_push($result['srcRoutes'], $srcString);
                            }
                            // Set feedback in properties json
                            $jsonProperties['textFeedbackTextarea'] = $odeComponentsSyncFeedbackHtmlView;
                        }

                        // Create jsonProperties for idevice
                        $jsonProperties = json_encode($jsonProperties);
                        $odeComponentsSync->setJsonProperties($jsonProperties);

                        // OdeComponentsSync property fields
                        $odeComponentsSync->loadOdeComponentsSyncPropertiesFromConfig();

                        // $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]["value"];
                        $subOdePagStructureSync->addOdeComponentsSync($odeComponentsSync);
                    }

                    array_push($result['odeComponentsSync'], $subOdePagStructureSync);
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
