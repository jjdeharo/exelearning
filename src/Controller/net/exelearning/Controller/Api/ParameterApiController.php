<?php

namespace App\Controller\net\exelearning\Controller\Api;

use App\Constants;
use App\Helper\net\exelearning\Helper\UserHelper;
use App\Properties;
use App\Settings;
use App\Util\net\exelearning\Util\UrlUtil;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/parameter-management/parameters')]
class ParameterApiController extends DefaultApiController
{
    private $router;
    private $translator;
    private $userHelper;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, UrlGeneratorInterface $router, TranslatorInterface $translator, UserHelper $userHelper)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->userHelper = $userHelper;

        parent::__construct($entityManager, $logger);
    }

    #[Route('/data/list', methods: ['GET'], name: 'api_parameters_data_list')]
    public function getParametersDataAction(Request $request)
    {
        $userLogged = $this->getUser();

        // Get properties of user
        $databaseUserPreferences = $this->userHelper->getUserPreferencesFromDatabase($userLogged);

        // Override the default locale of the application according to the user's language preferences
        if (!empty($databaseUserPreferences['locale'])) {
            $localeValue = $databaseUserPreferences['locale']->getValue();

            if (empty($localeValue)) {
                // Use default browser locale if user's locale is empty
                $localeValue = $request->getLocale();
            }

            $request->setLocale($localeValue);
            $request->setDefaultLocale($localeValue);
            $this->translator->setLocale($localeValue);
        }

        $data = [];

        $data['detectedLocale'] = $localeValue;
        $data['temporaryContentStorageDirectory'] = UrlUtil::getTemporaryContentStorageUrl();
        $data['generateNewItemKey'] = Constants::GENERATE_NEW_ITEM_KEY;
        $data['locales'] = Settings::LOCALES;
        $data['csvItemSeparator'] = Constants::CSV_ITEM_SEPARATOR;
        $data['versionControl'] = Settings::VERSION_CONTROL;
        $data['autosaveOdeFilesFunction'] = Settings::AUTOSAVE_ODE_FILES_FUNCTION;
        $data['autosaveIntervalTime'] = Settings::PERMANENT_SAVE_AUTOSAVE_TIME_INTERVAL;
        $data['countUserAutosave'] = Settings::COUNT_USER_AUTOSAVE_SPACE_ODE_FILES;

        // iDevice info
        $data['ideviceInfoFieldsConfig'] = $this->getProcessedPropertiesConfig(
            Properties::IDEVICE_INFO_FIELDS_CONFIG
        );

        // Theme info
        $data['themeInfoFieldsConfig'] = $this->getProcessedPropertiesConfig(
            Properties::THEME_INFO_FIELDS_CONFIG
        );

        // Theme edition
        $data['themeEditionFieldsConfig'] = $this->getProcessedPropertiesConfig(
            Properties::THEME_EDITION_FIELDS_CONFIG
        );

        // Properties
        $data['odeComponentsSyncPropertiesConfig'] = $this->getProcessedPropertiesConfig(
            Properties::ODE_COMPONENTS_SYNC_PROPERTIES_CONFIG
        );
        $data['odePagStructureSyncPropertiesConfig'] = $this->getProcessedPropertiesConfig(
            Properties::ODE_PAG_STRUCTURE_SYNC_PROPERTIES_CONFIG
        );
        $data['odeNavStructureSyncPropertiesConfig'] = $this->getProcessedPropertiesConfig(
            Properties::ODE_NAV_STRUCTURE_SYNC_PROPERTIES_CONFIG
        );
        $data['odeProjectSyncPropertiesConfig'] = $this->getProcessedPropertiesOdeConfig(
            Properties::ODE_PROPERTIES_CONFIG
        );
        $data['odeProjectSyncCataloguingConfig'] = $this->getProcessedPropertiesOdeConfig(
            Properties::ODE_CATALOGUING_CONFIG
        );

        // Preferences
        $data['userPreferencesConfig'] = $this->getProcessedPropertiesConfig(
            Properties::USER_PREFERENCES_CONFIG
        );

        $data['routes'] = [];

        $router = $this->router;
        $routes = $router->getRouteCollection();

        $basePath = $this->getParameter('base_path');

        foreach ($routes as $key => $route) {
            $path = $route->getPath();
            if (0 === strpos($path, $basePath.'/api/')) {
                $data['routes'][$key] = [
                    'path' => $path,
                    'methods' => $route->getMethods(),
                ];
            }
        }

        $jsonData = $this->getJsonSerialized($data);

        return new JsonResponse($jsonData, $this->status, [], true);
    }

    /**
     * Returns processed properties config of elements.
     *
     * @param array $configArray
     *
     * @return array
     */
    private function getProcessedPropertiesConfig($configArray)
    {
        $result = [];

        foreach ($configArray as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->getProcessedPropertiesConfig($value);
            } else {
                if (
                    (!empty($value))
                    && (0 === strpos($value, Properties::TRANS_PREFIX))
                ) {
                    $value = str_replace(Properties::TRANS_PREFIX, '', $value);
                    $value = $this->translator->trans($value);
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Returns processed ode properties config of elements.
     *
     * @param array $configArray
     *
     * @return array
     */
    private function getProcessedPropertiesOdeConfig($configArray)
    {
        $result = [];

        foreach ($configArray as $key => $value) {
            if ('category' == $key) {
                // Set category title
                $value = [$value => Properties::PROPERTIES_CATEGORIES_TITLE[$value]];
            }

            // Translations
            if (is_array($value)) {
                // Ode properties groups
                if ('groups' == $key) {
                    $newGroupsArray = [];
                    foreach ($value as $i => $groupKey) {
                        // Set group title
                        $newGroupsArray[$groupKey] = Properties::GROUPS_TITLE[$groupKey];
                    }
                    $value = $newGroupsArray;
                }
                $result[$key] = $this->getProcessedPropertiesOdeConfig($value);
            } else {
                if (
                    (!empty($value))
                    && (0 === strpos($value, Properties::TRANS_PREFIX))
                ) {
                    $value = str_replace(Properties::TRANS_PREFIX, '', $value);
                    $value = $this->translator->trans($value);
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * List of translations from properties and preferences arrays.
     */
    private function getProcessedPropertiesOdeConfigTranslations()
    {
        /*
        $this->translator->trans('Access');
        $this->translator->trans('Access Type');
        $this->translator->trans('active');
        $this->translator->trans('Search bar (Website export only)');
        $this->translator->trans('A search box will be added to every page of the website.');
        $this->translator->trans('Advanced (SEO)');
        $this->translator->trans('Advanced mode');
        $this->translator->trans('Aggregation Level');
        $this->translator->trans('Allows to minimize/display content');
        $this->translator->trans('analyse');
        $this->translator->trans('Annotation');
        $this->translator->trans('atomic');
        $this->translator->trans('Authorship');
        $this->translator->trans('Author URL');
        $this->translator->trans('blended');
        $this->translator->trans('browser');
        $this->translator->trans('Catalog');
        $this->translator->trans('Cataloging standard');
        $this->translator->trans('Cataloguing');
        $this->translator->trans('Classification');
        $this->translator->trans('classroom');
        $this->translator->trans('closed exercise or problem');
        $this->translator->trans('Cognitive Process');
        $this->translator->trans('collaborate');
        $this->translator->trans('collection');
        $this->translator->trans('communicate');
        $this->translator->trans('compare');
        $this->translator->trans('compete');
        $this->translator->trans('computer scientist');
        $this->translator->trans('conceptual map');
        $this->translator->trans('connect');
        $this->translator->trans('consider');
        $this->translator->trans('content provider');
        $this->translator->trans('Context');
        $this->translator->trans('contextualize');
        $this->translator->trans('contextualized case problem');
        $this->translator->trans('contribution');
        $this->translator->trans('Contribution');
        $this->translator->trans('control');
        $this->translator->trans('cooperate');
        $this->translator->trans('Copyright And Other Restrictions');
        $this->translator->trans('Cost');
        $this->translator->trans('Coverage');
        $this->translator->trans('Editable export');
        $this->translator->trans('The exported content will be editable with eXeLearning.');
        $this->translator->trans('"Made with eXeLearning" link');
        $this->translator->trans('Help us spreading eXeLearning. Checking this option, a "Made with eXeLearning" link will be displayed in your pages.');
        $this->translator->trans('Include a copy of the source file');
        $this->translator->trans('A copy of the eXeLearning file will be included when exporting the content.');
        $this->translator->trans('Accessibility toolbar');
        $this->translator->trans('The accessibility toolbar allows visitors to manipulate some aspects of your site, such as font and text size.');
        $this->translator->trans('creator');
        $this->translator->trans('CSS Class');
        $this->translator->trans('Days');
        $this->translator->trans('decide');
        $this->translator->trans('Language');
        $this->translator->trans('License for the new documents');
        $this->translator->trans('You can choose a different language for the current project.');
        $this->translator->trans('define');
        $this->translator->trans('describe');
        $this->translator->trans('Description');
        $this->translator->trans('design');
        $this->translator->trans('diagram');
        $this->translator->trans('didactic game');
        $this->translator->trans('Different title on the page');
        $this->translator->trans('difficult');
        $this->translator->trans('Difficulty');
        $this->translator->trans('discuss');
        $this->translator->trans('discussion activity');
        $this->translator->trans('distance');
        $this->translator->trans('draft');
        $this->translator->trans('dual free content license GPL and EUPL');
        $this->translator->trans('easy');
        $this->translator->trans('education expert');
        $this->translator->trans('Educational');
        $this->translator->trans('educational validator');
        $this->translator->trans('Email');
        $this->translator->trans('Entity');
        $this->translator->trans('Entry');
        $this->translator->trans('exam');
        $this->translator->trans('exercise');
        $this->translator->trans('experiment');
        $this->translator->trans('explain');
        $this->translator->trans('expositive');
        $this->translator->trans('extrapolate');
        $this->translator->trans('face to face');
        $this->translator->trans('family');
        $this->translator->trans('figure');
        $this->translator->trans('final');
        $this->translator->trans('For Group Work');
        $this->translator->trans('For Individual Tuition');
        $this->translator->trans('free software license EUPL');
        $this->translator->trans('free software license GPL');
        $this->translator->trans('General');
        $this->translator->trans('General description');
        $this->translator->trans('general public');
        $this->translator->trans('gifted learner');
        $this->translator->trans('graph');
        $this->translator->trans('graphical designer');
        $this->translator->trans('group');
        $this->translator->trans('guided reading');
        $this->translator->trans('Header');
        $this->translator->trans('Header image');
        $this->translator->trans('hierarchical');
        $this->translator->trans('high');
        $this->translator->trans('ID');
        $this->translator->trans('Identifier');
        $this->translator->trans('implement');
        $this->translator->trans('independent');
        $this->translator->trans('index');
        $this->translator->trans('individual');
        $this->translator->trans('information scientist');
        $this->translator->trans('initiator');
        $this->translator->trans('innovate');
        $this->translator->trans('Installation Remarks');
        $this->translator->trans('Installation remarks');
        $this->translator->trans('instructional designer');
        $this->translator->trans('intellectual property license');
        $this->translator->trans('Intended End User Role');
        $this->translator->trans('Intended User Role');
        $this->translator->trans('Interactive Type');
        $this->translator->trans('Interactivity Level');
        $this->translator->trans('Interactivity Type');
        $this->translator->trans('investigate');
        $this->translator->trans('judge');
        $this->translator->trans('Keyword');
        $this->translator->trans('Kind');
        $this->translator->trans('laboratory');
        $this->translator->trans('learner');
        $this->translator->trans('learner with other specific educational support needs');
        $this->translator->trans('learners late integration into the education system');
        $this->translator->trans('learning objects');
        $this->translator->trans('Learning Resource Type');
        $this->translator->trans('lecture');
        $this->translator->trans('license GFDL');
        $this->translator->trans('License URL');
        $this->translator->trans('Life Cycle');
        $this->translator->trans('linear');
        $this->translator->trans('Links color');
        $this->translator->trans('Location');
        $this->translator->trans('Logo image');
        $this->translator->trans('low');
        $this->translator->trans('manager');
        $this->translator->trans('master class');
        $this->translator->trans('Maximum version');
        $this->translator->trans('Meta-Metadata');
        $this->translator->trans('Metadata Schema');
        $this->translator->trans('Minimized');
        $this->translator->trans('Minimum version');
        $this->translator->trans('mixed');
        $this->translator->trans('Modality');
        $this->translator->trans('Months');
        $this->translator->trans('motivate');
        $this->translator->trans('narrative text');
        $this->translator->trans('networked');
        $this->translator->trans('non-universal');
        $this->translator->trans('not appropriate');
        $this->translator->trans('observe');
        $this->translator->trans('open problem');
        $this->translator->trans('operating system');
        $this->translator->trans('ordinary learner');
        $this->translator->trans('Organization');
        $this->translator->trans('organize');
        $this->translator->trans('organize oneself');
        $this->translator->trans('other free software licenses');
        $this->translator->trans('Other Platforms Requirement');
        $this->translator->trans('Other platforms requirements');
        $this->translator->trans('Package');
        $this->translator->trans('plan');
        $this->translator->trans('practise');
        $this->translator->trans('Preknowledge');
        $this->translator->trans('presencial');
        $this->translator->trans('Primary author of the resource.');
        $this->translator->trans('problem statement');
        $this->translator->trans('produce');
        $this->translator->trans('proprietary license');
        $this->translator->trans('prove');
        $this->translator->trans('publisher');
        $this->translator->trans('Purpose');
        $this->translator->trans('questionnaire');
        $this->translator->trans('real environment');
        $this->translator->trans('real or virtual learning environment');
        $this->translator->trans('real project');
        $this->translator->trans('recognize');
        $this->translator->trans('Relation');
        $this->translator->trans('remember');
        $this->translator->trans('represent');
        $this->translator->trans('Requirement');
        $this->translator->trans('Resource');
        $this->translator->trans('resources and integrated resources');
        $this->translator->trans('revised');
        $this->translator->trans('Rights');
        $this->translator->trans('Role');
        $this->translator->trans('Schema');
        $this->translator->trans('schoolmate');
        $this->translator->trans('script writer');
        $this->translator->trans('Select a language.');
        $this->translator->trans('self assessment');
        $this->translator->trans('Semantic Density');
        $this->translator->trans('simulate');
        $this->translator->trans('simulation');
        $this->translator->trans('slide');
        $this->translator->trans('solve');
        $this->translator->trans('Source');
        $this->translator->trans('special needs learner');
        $this->translator->trans('subject matter expert');
        $this->translator->trans('summarize');
        $this->translator->trans('table');
        $this->translator->trans('Taxon');
        $this->translator->trans('Taxon Path');
        $this->translator->trans('teacher');
        $this->translator->trans('teaching sequences');
        $this->translator->trans('Technical');
        $this->translator->trans('technical implementer');
        $this->translator->trans('technical validator');
        $this->translator->trans('terminator');
        $this->translator->trans('Texts and Links');
        $this->translator->trans('textual-image analysis');
        $this->translator->trans('The current document license can be modified in the project properties.');
        $this->translator->trans('The name given to the resource.');
        $this->translator->trans('Title HTML');
        $this->translator->trans('Title in page');
        $this->translator->trans('training programmes, courses and plans');
        $this->translator->trans('tutor');
        $this->translator->trans('Typical Age Range');
        $this->translator->trans('Typical Learning Time');
        $this->translator->trans('unavailable');
        $this->translator->trans('understand');
        $this->translator->trans('universal');
        $this->translator->trans('Content metadata');
        $this->translator->trans('Usage');
        $this->translator->trans('Export options');
        $this->translator->trans('validator');
        $this->translator->trans('value');
        $this->translator->trans('Version control');
        $this->translator->trans('very difficult');
        $this->translator->trans('very easy');
        $this->translator->trans('very high');
        $this->translator->trans('very low');
        $this->translator->trans('Visible in export');
        $this->translator->trans('webquest');
        $this->translator->trans('write up');
        $this->translator->trans('Years');
        $this->translator->trans('public domain');
        $this->translator->trans('yes');
        $this->translator->trans('Page counter');
        $this->translator->trans('A text with the page number will be added on each page.');
        $this->translator->trans('discipline');
        $this->translator->trans('idea');
        $this->translator->trans('prerequisite');
        $this->translator->trans('educational objective');
        $this->translator->trans('accessibility restrictions');
        $this->translator->trans('educational level');
        $this->translator->trans('skill level');
        $this->translator->trans('security level');
        $this->translator->trans('competency');
        */
        
        // USER PREFERENCES
        // General settings
        $this->translator->trans('General settings');
        $this->translator->trans('Language');
        $this->translator->trans('You can choose a different language for the current project.');
        $this->translator->trans('License for the new documents');
        $this->translator->trans('You can choose a different licence for the current project.');
        $this->translator->trans('Style theme'); // To review
        $this->translator->trans('Version control');
        
        // STYLES CONFIG (to review)
        // Edition theme categories
        $this->translator->trans('Information');
        $this->translator->trans('Texts and Links');
        $this->translator->trans('Header');
        
        // Theme info fields
        $this->translator->trans('Title');
        $this->translator->trans('Description');
        $this->translator->trans('Version');
        $this->translator->trans('Authorship');
        $this->translator->trans('Author URL');
        $this->translator->trans('License');
        $this->translator->trans('License URL');
        
        // Theme edition fields
        // $this->translator->trans('Title');
        // $this->translator->trans('Description');
        // $this->translator->trans('Version');
        // $this->translator->trans('Authorship');
        // $this->translator->trans('Author URL');
        // $this->translator->trans('License');
        // $this->translator->trans('License URL');
        $this->translator->trans('Text color');
        $this->translator->trans('Links color');
        $this->translator->trans('Header image');
        $this->translator->trans('Logo image');
        
        // IDEVICE INFO FIELDS
        // $this->translator->trans('Title');
        // $this->translator->trans('Description');
        // $this->translator->trans('Version');
        // $this->translator->trans('Authorship');
        // $this->translator->trans('Author URL');
        // $this->translator->trans('License');
        // $this->translator->trans('License URL');
        
        // IDEVICE PROPERTIES
        $this->translator->trans('General');
        $this->translator->trans('Visible in export');
        $this->translator->trans('ID');
        $this->translator->trans('CSS Class');

        // BOX PROPERTIES
        // $this->translator->trans('Visible in export');
        $this->translator->trans('Allows to minimize/display content');
        $this->translator->trans('Minimized');
        // $this->translator->trans('ID');
        // $this->translator->trans('CSS Class');

        // PAGES PROPERTIES
        // $this->translator->trans('Title');
        $this->translator->trans('Title HTML');
        $this->translator->trans('Advanced (SEO)');
        $this->translator->trans('Different title on the page');

        $this->translator->trans('Title in page');
        // $this->translator->trans('Visible in export');
        // $this->translator->trans('Description');
        // $this->translator->trans('Advanced (SEO)');
        
        // PACKAGE PROPERTIES
        
        // Content metadata
        $this->translator->trans('Content metadata');
        // $this->translator->trans('Title');
        $this->translator->trans('The name given to the resource.');
        // $this->translator->trans('Language');
        $this->translator->trans('Select a language.');
        // $this->translator->trans('Authorship');
        $this->translator->trans('Primary author/s of the resource.');
        // $this->translator->trans('License');
        // $this->translator->trans('Description');
        
        // Export options
        $this->translator->trans('Editable export');
        $this->translator->trans('The exported content will be editable with eXeLearning.');
        $this->translator->trans('"Made with eXeLearning" link');
        $this->translator->trans('Help us spreading eXeLearning. Checking this option, a "Made with eXeLearning" link will be displayed in your pages.');
        $this->translator->trans('Include a copy of the source file');
        $this->translator->trans('A copy of the eXeLearning file will be included when exporting the content.');
        $this->translator->trans('Page counter');
        $this->translator->trans('A text with the page number will be added on each page.');
        $this->translator->trans('Search bar (Website export only)');
        $this->translator->trans('A search box will be added to every page of the website.');
        $this->translator->trans('Accessibility toolbar');
        $this->translator->trans('The accessibility toolbar allows visitors to manipulate some aspects of your site, such as font and text size.');
        
        // Custom code
        $this->translator->trans('Custom code');
        $this->translator->trans('HEAD');
        $this->translator->trans('HTML to be included at the end of the HEAD tag: LINK, META, SCRIPT, STYLE...');
        $this->translator->trans('Page footer');
        $this->translator->trans('Type any HTML. It will be placed after every page content. No JavaScript code will be executed inside eXe.');
        
        // To review (this string should only be in app.js, but it's not extracted)
        $this->translator->trans('eXeLearning %s is a development version. It is not for production use.');
        $this->translator->trans('This is just a demo version. Not for real projects. Days before it expires: %s');
        $this->translator->trans('eXeLearning %s has expired! Please download the latest version.');
    }
}
