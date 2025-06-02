<?php

namespace App\Util\net\exelearning\Util\OdeOldXmlIdevices;

use App\Constants;
use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use App\Util\net\exelearning\Util\UrlUtil;
use App\Util\net\exelearning\Util\Util;

/**
 * OdeOldXmlMultipleSelectionIdevice.
 */
class OdeOldXmlMultipleSelectionIdevice
{
    public const JSON_QUESTIONS = [
        'activityType' => 'selection',
        'selectionType' => 'single',
        'baseText' => '',
        'answers' => ['{{selectionAnswers}}'],
    ];

    // Create jsonProperties for idevice
    public const JSON_PROPERTIES = [
        'ideviceId' => '',
        'eXeFormInstructions' => '',
        'questionsData' => ['{{addQuestions}}'],
        'dropdownPassRate' => '',
        'checkAddBtnAnswers' => true,
        'userTranslations' => [
            'langTrueFalseHelp' => 'Select whether the statement is true or false',
            'langDropdownHelp' => 'Choose the correct answer among the options proposed',
            'langSingleSelectionHelp' => 'Multiple choice with only one correct answer',
            'langMultipleSelectionHelp' => 'Multiple choice with multiple corrects answers',
            'langFillHelp' => 'Fill in the blanks with the appropriate word',
        ],
    ];

    // Old Xml idevice content
    public const OLD_ODE_XML_INSTANCE = 'instance';
    public const OLD_ODE_XML_DICTIONARY = 'dictionary';
    public const OLD_ODE_XML_LIST = 'list';
    public const OLD_ODE_XML_UNICODE = 'unicode';
    public const OLD_ODE_XML_ATTRIBUTES = '@attributes';
    // const OLD_ODE_XML_IDEVICE_TEXT = 'instance';
    public const OLD_ODE_XML_IDEVICE_TEXT_CONTENT = 'string role="key" value="content_w_resourcePaths"';

    public static function oldElpMultipleSelectionStructure($odeSessionId, $odePageId, $caseStudyNodes, $generatedIds, $xpathNamespace)
    {
        $result['odeComponentsSync'] = [];
        $result['srcRoutes'] = [];

        foreach ($caseStudyNodes as $caseStudyNode) {
            $fullHtmlView = [];
            $fullHtmlFeedbackView = [];
            $nodeQuestions = [];
            $nodeTasks = [];
            $truefalseIdeviceHtmlIstructions = '';
            $fullOdeComponentsSyncHtmlFeedbackView = '';
            $fullOdeComponentsSyncHtmlView = '';
            $truefalseIdeviceHtmlIstructions = '';

            $odeIdeviceId = Util::generateIdCheckUnique($generatedIds);
            $generatedIds[] = $odeIdeviceId;
            $odeComponentsMapping[] = $odeIdeviceId;

            $caseStudyNode->registerXPathNamespace('f', $xpathNamespace);
            // Get blockName
            $blockNameNode = $caseStudyNode->xpath("f:dictionary/f:string[@value='_title']/following-sibling::f:unicode[1]/@value");

            $nodeIdevices = $caseStudyNode->xpath("f:dictionary/f:list/f:instance[@class='exe.engine.field.QuizQuestionField']");

            $nodeIdeviceQuestions = $caseStudyNode->xpath("f:dictionary/f:list/
            f:instance[@class='exe.engine.truefalseidevice.TrueFalseQuestion']");
            $nodeIdevicesFeedback = $caseStudyNode->xpath(
                "f:dictionary/f:list/f:instance[@class='exe.engine.casestudyidevice.Question']/f:dictionary/f:string[@value='feedbackTextArea']/following-sibling::
                f:instance[1][@class='exe.engine.field.TextAreaField']"
            );

            foreach ($nodeIdevices as $nodeIdevice) {
                $questionAnswers = [];
                $nodeIdevice->registerXPathNamespace('f', $xpathNamespace);
                $nodeIdeviceQuestionTextAreaHtmlContent = $nodeIdevice->xpath("f:dictionary/f:string[@value='questionTextArea']/
                following-sibling::f:instance[1]")[0];
                $nodeIdeviceQuestionAnswers = $nodeIdevice->xpath("f:dictionary/f:string[@value='options']/
                following-sibling::f:list/f:instance[@class='exe.engine.field.QuizOptionField']");

                $nodeIdeviceQuestionTextAreaHtmlContent->registerXPathNamespace('f', $xpathNamespace);
                $textAreaHtmlContent = $nodeIdeviceQuestionTextAreaHtmlContent->xpath("f:dictionary/f:string[@value='content_w_resourcePaths']/
                following-sibling::f:unicode[@content='true']");

                $sessionPath = null;

                if (!empty($odeSessionId)) {
                    $sessionPath = UrlUtil::getOdeSessionUrl($odeSessionId);
                }

                // Common replaces for all OdeComponents
                $commonReplaces = [
                    'resources'.Constants::SLASH => $sessionPath.$odeIdeviceId.Constants::SLASH,
                ];

                if (isset($commonReplaces)) {
                    $odeComponentsSyncHtmlView = self::applyReplaces(
                        $commonReplaces,
                        (string) $textAreaHtmlContent[0]['value']
                    );
                    array_push($fullHtmlView, $odeComponentsSyncHtmlView);
                } else {
                    $odeComponentsSyncHtmlView = (string) $textAreaHtmlContent[0]['value'];
                    array_push($fullHtmlView, $odeComponentsSyncHtmlView);
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

                foreach ($fullHtmlView as $htmlView) {
                    $truefalseIdeviceHtmlIstructions = $htmlView;
                }

                // Answers
                foreach ($nodeIdeviceQuestionAnswers as $nodeIdeviceQuestionAnswer) {
                    $nodeIdeviceQuestionAnswer->registerXPathNamespace('f', $xpathNamespace);

                    // $nodeReferenceKey = $nodeIdeviceQuestionAnswer->xpath("f:dictionary/f:string[@value='_idevice']/
                    // following-sibling::f:reference[1]/@key");

                    $nodeIdeviceQuestionAnswerHtmlContent = $nodeIdeviceQuestionAnswer->xpath("f:dictionary/f:string[@value='answerTextArea']/following-sibling::f:instance[1]/
                    f:dictionary/f:string[@value='content_w_resourcePaths']/following-sibling::f:unicode[@content='true']")[0];

                    $nodeIdeviceQuestionAnswersIsCorrect = $nodeIdeviceQuestionAnswer->xpath("f:dictionary/f:string[@value='isCorrect']/
                    following-sibling::f:bool/@value")[0];

                    if ('0' == (string) $nodeIdeviceQuestionAnswersIsCorrect) {
                        $nodeIdeviceQuestionAnswersIsCorrect = 'false';
                    } else {
                        $nodeIdeviceQuestionAnswersIsCorrect = 'true';
                    }

                    $questionsAnswerArrayStructure = [
                        $nodeIdeviceQuestionAnswersIsCorrect, '"'.$nodeIdeviceQuestionAnswerHtmlContent['value'].'"',
                    ];
                    array_push($questionAnswers, $questionsAnswerArrayStructure);
                }

                // Apply changes to json properties to add questions
                $ideviceAnswers = '';
                foreach ($questionAnswers as $questionAnswer) {
                    $answerValue = strip_tags($questionAnswer[1]);
                    $ideviceAnswers .= '['.$questionAnswer[0].','.$answerValue.'],';
                }
                $jsonAnswers = rtrim($ideviceAnswers, ',');
                $changes = ['"{{selectionAnswers}}"' => $jsonAnswers];

                $jsonQuestions = self::JSON_QUESTIONS;
                $jsonQuestions['baseText'] = $truefalseIdeviceHtmlIstructions;
                $jsonQuestions = json_encode($jsonQuestions);
                $jsonQuestions = self::applyHtmlChange($changes, $jsonQuestions);

                array_push($nodeTasks, $jsonQuestions);
            }

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

                // OdeComponentsSync fields
                $odeComponentsSync->setOdeSessionId($odeSessionId);
                $odeComponentsSync->setOdePageId($odePageId);
                $odeComponentsSync->setOdeBlockId($odeBlockId);
                $odeComponentsSync->setOdeIdeviceId($odeIdeviceId);

                // $odeComponentsSync->setJsonProperties($odeComponentsSyncJsonProperties);

                $odeComponentsSync->setOdeComponentsSyncOrder(intval(1));
                // Set type
                $odeComponentsSync->setOdeIdeviceTypeName('form');

                // $odeComponentsSync->setHtmlView($fullOdeComponentsSyncHtmlView);

                $jsonProperties = self::JSON_PROPERTIES;
                $jsonProperties['ideviceId'] = $odeIdeviceId;
                $truefalseIdeviceHtmlIstructions = strip_tags($truefalseIdeviceHtmlIstructions);
                // $jsonProperties["eXeFormInstructions"] = $truefalseIdeviceHtmlIstructions;
                // $jsonProperties["textFeedbackTextarea"] = $fullOdeComponentsSyncHtmlFeedbackView;

                $jsonProperties = json_encode($jsonProperties);
                $fullJsonQuestions = '';
                foreach ($nodeTasks as $nodeTask) {
                    $fullJsonQuestions .= $nodeTask.',';
                }
                $fullJsonQuestions = rtrim($fullJsonQuestions, ',');
                $changesJson = ['"{{addQuestions}}"' => $fullJsonQuestions];
                $jsonProperties = self::applyHtmlChange($changesJson, $jsonProperties);

                // Create jsonProperties for idevice
                $odeComponentsSync->setJsonProperties($jsonProperties);

                // OdeComponentsSync property fields
                $odeComponentsSync->loadOdeComponentsSyncPropertiesFromConfig();

                // $oldXmlListDictListInstDictListInstDict->{self::OLD_ODE_XML_UNICODE}[1]["value"];
                $subOdePagStructureSync->addOdeComponentsSync($odeComponentsSync);

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

    /**
     * Change correct value to "True" or "False".
     *
     * @param string $trueFalseCorrectValue
     *
     * @return string
     */
    private static function transformTrueFalseCorrectValue($trueFalseCorrectValue)
    {
        // By default on empty value we return False as correct value
        if (empty($trueFalseCorrectValue)) {
            return 'False';
        }

        // Check value is "0" or "1" to set "False" or "True"
        if ('0' == $trueFalseCorrectValue) {
            return 'False';
        } else {
            return 'True';
        }
    }
}
