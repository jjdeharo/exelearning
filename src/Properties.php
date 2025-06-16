<?php

namespace App;

/**
 * Properties and preferences arrays.
 */
class Properties
{
    // Elements properties config
    public const TRANS_PREFIX = 'TRANSLATABLE_TEXT:';

    /*********************************************************************************
     * USER PREFERENCES
     *********************************************************************************/

    public const USER_PREFERENCES_CONFIG = [
        'locale' => [
            'title' => self::TRANS_PREFIX.'Language',
            'help' => self::TRANS_PREFIX.'You can choose a different language for the current project.',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => self::TRANS_PREFIX.'General settings',
        ],
        'advancedMode' => [
            'title' => self::TRANS_PREFIX.'Advanced mode',
            'value' => 'true',
            'type' => 'checkbox',
            'hide' => true,
            'category' => self::TRANS_PREFIX.'General settings',
        ],
        'defaultLicense' => [
            'title' => self::TRANS_PREFIX.'License for the new documents',
            'help' => self::TRANS_PREFIX.'You can choose a different licence for the current project.',
            'value' => 'creative commons: attribution - share alike 4.0',
            'type' => 'select',
            'options' => self::LICENSES,
            // 'category' => self::TRANS_PREFIX.'Advanced',
            'category' => self::TRANS_PREFIX.'General settings',
        ],
        'theme' => [
            'title' => self::TRANS_PREFIX.'Style theme',
            'value' => Constants::THEME_DEFAULT,
            'type' => 'text',
            'hide' => true,
            // 'category' => self::TRANS_PREFIX.'Advanced',
            'category' => self::TRANS_PREFIX.'General settings',
        ],
        'versionControl' => [
            'title' => self::TRANS_PREFIX.'Version control',
            'value' => "'".Settings::VERSION_CONTROL."'",
            'type' => 'checkbox',
            // 'category' => self::TRANS_PREFIX.'Advanced',
            'category' => self::TRANS_PREFIX.'General settings',
        ],
    ];

    /*********************************************************************************
     * THEMES CONFIG
     *********************************************************************************/

    // Edition theme categories
    public const THEME_EDITION_CATEGORIES = [
        'information' => self::TRANS_PREFIX.'Information',
        'texts-links' => self::TRANS_PREFIX.'Texts and Links',
        'header-footer' => self::TRANS_PREFIX.'Header',
    ];

    // Theme info fields
    public const THEME_INFO_FIELDS_CONFIG = [
        'title' => [
            'title' => self::TRANS_PREFIX.'Title',
            'tag' => 'text',
        ],
        'description' => [
            'title' => self::TRANS_PREFIX.'Description',
            'tag' => 'textarea',
        ],
        'version' => [
            'title' => self::TRANS_PREFIX.'Version',
            'tag' => 'text',
        ],
        'author' => [
            'title' => self::TRANS_PREFIX.'Authorship',
            'tag' => 'text',
        ],
        'authorUrl' => [
            'title' => self::TRANS_PREFIX.'Author URL',
            'tag' => 'text',
        ],
        'license' => [
            'title' => self::TRANS_PREFIX.'License',
            'tag' => 'textarea',
        ],
        'licenseUrl' => [
            'title' => self::TRANS_PREFIX.'License URL',
            'tag' => 'textarea',
        ],
    ];

    // Theme edition fields
    public const THEME_EDITION_FIELDS_CONFIG = [
        'title' => [
            'title' => self::TRANS_PREFIX.'Title',
            'tag' => 'text',
            'config' => 'title',
            'category' => self::THEME_EDITION_CATEGORIES['information'],
        ],
        'description' => [
            'title' => self::TRANS_PREFIX.'Description',
            'tag' => 'textarea',
            'config' => 'description',
            'category' => self::THEME_EDITION_CATEGORIES['information'],
        ],
        'version' => [
            'title' => self::TRANS_PREFIX.'Version',
            'tag' => 'text',
            'config' => 'version',
            'category' => self::THEME_EDITION_CATEGORIES['information'],
        ],
        'author' => [
            'title' => self::TRANS_PREFIX.'Authorship',
            'tag' => 'text',
            'config' => 'author',
            'category' => self::THEME_EDITION_CATEGORIES['information'],
        ],
        'authorUrl' => [
            'title' => self::TRANS_PREFIX.'Author URL',
            'tag' => 'text',
            'config' => 'author-url',
            'category' => self::THEME_EDITION_CATEGORIES['information'],
        ],
        'license' => [
            'title' => self::TRANS_PREFIX.'License',
            'tag' => 'textarea',
            'config' => 'license',
            'category' => self::THEME_EDITION_CATEGORIES['information'],
        ],
        'licenseUrl' => [
            'title' => self::TRANS_PREFIX.'License URL',
            'tag' => 'textarea',
            'config' => 'license-url',
            'category' => self::THEME_EDITION_CATEGORIES['information'],
        ],
        'textColor' => [
            'title' => self::TRANS_PREFIX.'Text color',
            'tag' => 'color',
            'config' => 'text-color',
            'category' => self::THEME_EDITION_CATEGORIES['texts-links'],
        ],
        'linkColor' => [
            'title' => self::TRANS_PREFIX.'Links color',
            'tag' => 'color',
            'config' => 'link-color',
            'category' => self::THEME_EDITION_CATEGORIES['texts-links'],
        ],
        'headerImg' => [
            'title' => self::TRANS_PREFIX.'Header image',
            'tag' => 'img',
            'config' => 'header-img',
            'category' => self::THEME_EDITION_CATEGORIES['header-footer'],
        ],
        'logoImg' => [
            'title' => self::TRANS_PREFIX.'Logo image',
            'tag' => 'img',
            'config' => 'logo-img',
            'category' => self::THEME_EDITION_CATEGORIES['header-footer'],
        ],
    ];

    /*********************************************************************************
     * IDEVICES INFO
     *********************************************************************************/

    // Idevice info fields
    public const IDEVICE_INFO_FIELDS_CONFIG = [
        'title' => [
            'title' => self::TRANS_PREFIX.'Title',
            'tag' => 'text',
        ],
        'description' => [
            'title' => self::TRANS_PREFIX.'Description',
            'tag' => 'textarea',
        ],
        'version' => [
            'title' => self::TRANS_PREFIX.'Version',
            'tag' => 'text',
        ],
        'author' => [
            'title' => self::TRANS_PREFIX.'Authorship',
            'tag' => 'text',
        ],
        'authorUrl' => [
            'title' => self::TRANS_PREFIX.'Author URL',
            'tag' => 'text',
        ],
        'license' => [
            'title' => self::TRANS_PREFIX.'License',
            'tag' => 'textarea',
        ],
        'licenseUrl' => [
            'title' => self::TRANS_PREFIX.'License URL',
            'tag' => 'textarea',
        ],
    ];

    /*********************************************************************************
     * IDEVICES COMPONENTS PROPERTIES
     *********************************************************************************/

    public const ODE_COMPONENTS_SYNC_PROPERTIES_CONFIG = [
        'visibility' => [
            'title' => self::TRANS_PREFIX.'Visible in export',
            'value' => 'true',
            'type' => 'checkbox',
            'category' => null,
            'heritable' => true,
        ],
        'identifier' => [
            'title' => self::TRANS_PREFIX.'ID',
            'type' => 'text',
            'category' => null,
            'heritable' => false,
        ],
        'cssClass' => [
            'title' => self::TRANS_PREFIX.'CSS Class',
            'value' => '',
            'type' => 'text',
            'category' => null,
            'heritable' => true,
        ],
    ];

    /*********************************************************************************
     * BLOCK COMPONENTS PROPERTIES
     *********************************************************************************/

    public const ODE_PAG_STRUCTURE_SYNC_PROPERTIES_CONFIG = [
        'visibility' => [
            'title' => self::TRANS_PREFIX.'Visible in export',
            'value' => 'true',
            'type' => 'checkbox',
            'category' => null,
            'heritable' => true,
        ],
        'allowToggle' => [
            'title' => self::TRANS_PREFIX.'Allows to minimize/display content',
            'value' => 'true',
            'type' => 'checkbox',
            'category' => null,
            'heritable' => true,
        ],
        'minimized' => [
            'title' => self::TRANS_PREFIX.'Minimized',
            'value' => 'false',
            'type' => 'checkbox',
            'category' => null,
            'heritable' => true,
        ],
        'identifier' => [
            'title' => self::TRANS_PREFIX.'ID',
            'type' => 'text',
            'category' => null,
            'heritable' => false,
        ],
        'cssClass' => [
            'title' => self::TRANS_PREFIX.'CSS Class',
            'value' => '',
            'type' => 'text',
            'category' => null,
            'heritable' => true,
        ],
    ];

    /*********************************************************************************
     * PAGES COMPONENTS PROPERTIES
     *********************************************************************************/

    public const ODE_NAV_STRUCTURE_SYNC_PROPERTIES_CONFIG = [
        'titleNode' => [
            'title' => self::TRANS_PREFIX.'Title',
            'type' => 'text',
            'category' => self::TRANS_PREFIX.'General',
            'heritable' => false,
        ],
        'titleHtml' => [
            'title' => self::TRANS_PREFIX.'Title HTML',
            'type' => 'text',
            'category' => self::TRANS_PREFIX.'Advanced (SEO)',
            'heritable' => false,
        ],
        'editableInPage' => [
            'title' => self::TRANS_PREFIX.'Different title on the page',
            'type' => 'checkbox',
            'category' => self::TRANS_PREFIX.'General',
            'value' => 'false',
            'alwaysVisible' => true,
        ],
        'titlePage' => [
            'title' => self::TRANS_PREFIX.'Title in page',
            'type' => 'text',
            'category' => self::TRANS_PREFIX.'General',
            'heritable' => false,
        ],
        'visibility' => [
            'title' => self::TRANS_PREFIX.'Visible in export',
            'value' => 'true',
            'type' => 'checkbox',
            'category' => self::TRANS_PREFIX.'General',
            'heritable' => true,
        ],
        'description' => [
            'title' => self::TRANS_PREFIX.'Description',
            'type' => 'textarea',
            'category' => self::TRANS_PREFIX.'Advanced (SEO)',
            'heritable' => false,
        ],
    ];

    /*********************************************************************************
     * OPTIONS - PREFERENCES
     *********************************************************************************/

    public const LICENSES = [
        'creative commons: attribution 4.0' => self::TRANS_PREFIX.'creative commons: attribution 4.0 (BY)',
        'creative commons: attribution - share alike 4.0' => self::TRANS_PREFIX.'creative commons: attribution - share alike 4.0 (BY-SA)',
        'creative commons: attribution - non derived work 4.0' => self::TRANS_PREFIX.'creative commons: attribution - non derived work 4.0 (BY-ND)',
        'creative commons: attribution - non commercial 4.0' => self::TRANS_PREFIX.'creative commons: attribution - non commercial 4.0 (BY-NC)',
        'creative commons: attribution - non commercial - share alike 4.0' => self::TRANS_PREFIX.'creative commons: attribution - non commercial - share alike 4.0 (BY-NC-SA)',
        'creative commons: attribution - non derived work - non commercial 4.0' => self::TRANS_PREFIX.'creative commons: attribution - non derived work - non commercial 4.0 (BY-NC-ND)',
        'public domain' => self::TRANS_PREFIX.'public domain',
        'free software license EUPL' => self::TRANS_PREFIX.'free software license EUPL',
        'free software license GPL' => self::TRANS_PREFIX.'free software license GPL',
        'dual free content license GPL and EUPL' => self::TRANS_PREFIX.'dual free content license GPL and EUPL',
        'license GFDL' => self::TRANS_PREFIX.'license GFDL',
        'other free software licenses' => self::TRANS_PREFIX.'other free software licenses',
        'propietary license' => self::TRANS_PREFIX.'proprietary license',
        'intellectual property license' => self::TRANS_PREFIX.'intellectual property license',
        'not appropriate' => self::TRANS_PREFIX.'not appropriate',
        // Old licenses
        'creative commons: attribution 3.0' => self::TRANS_PREFIX.'creative commons: attribution 3.0 (BY)',
        'creative commons: attribution - share alike 3.0' => self::TRANS_PREFIX.'creative commons: attribution - share alike 3.0 (BY-SA)',
        'creative commons: attribution - non derived work 3.0' => self::TRANS_PREFIX.'creative commons: attribution - non derived work 3.0 (BY-ND)',
        'creative commons: attribution - non commercial 3.0' => self::TRANS_PREFIX.'creative commons: attribution - non commercial 3.0 (BY-NC)',
        'creative commons: attribution - non commercial - share alike 3.0' => self::TRANS_PREFIX.'creative commons: attribution - non commercial - share alike 3.0 (BY-NC-SA)',
        'creative commons: attribution - non derived work - non commercial 3.0' => self::TRANS_PREFIX.'creative commons: attribution - non derived work - non commercial 3.0 (BY-NC-ND)',
        'creative commons: attribution 2.5' => self::TRANS_PREFIX.'creative commons: attribution 2.5 (BY)',
        'creative commons: attribution - share alike 2.5' => self::TRANS_PREFIX.'creative commons: attribution - share alike 2.5 (BY-SA)',
        'creative commons: attribution - non derived work 2.5' => self::TRANS_PREFIX.'creative commons: attribution - non derived work 2.5 (BY-ND)',
        'creative commons: attribution - non commercial 2.5' => self::TRANS_PREFIX.'creative commons: attribution - non commercial 2.5 (BY-NC)',
        'creative commons: attribution - non commercial - share alike 2.5' => self::TRANS_PREFIX.'creative commons: attribution - non commercial - share alike 2.5 (BY-NC-SA)',
        'creative commons: attribution - non derived work - non commercial 2.5' => self::TRANS_PREFIX.'creative commons: attribution - non derived work - non commercial 2.5 (BY-NC-ND)',
    ];

    public const LICENSES_LINKS = [
        'creative commons: attribution 4.0' => 'https://creativecommons.org/licenses/by/4.0/',
        'creative commons: attribution - share alike 4.0' => 'https://creativecommons.org/licenses/by-sa/4.0/',
        'creative commons: attribution - non derived work 4.0' => 'https://creativecommons.org/licenses/by-nd/4.0/',
        'creative commons: attribution - non commercial 4.0' => 'https://creativecommons.org/licenses/by-nc/4.0/',
        'creative commons: attribution - non commercial - share alike 4.0' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
        'creative commons: attribution - non derived work - non commercial 4.0' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/',
        // Old licenses
        'creative commons: attribution 3.0' => 'https://creativecommons.org/licenses/by/3.0/',
        'creative commons: attribution - share alike 3.0' => 'https://creativecommons.org/licenses/by-sa/3.0/',
        'creative commons: attribution - non derived work 3.0' => 'https://creativecommons.org/licenses/by-nd/3.0/',
        'creative commons: attribution - non commercial 3.0' => 'https://creativecommons.org/licenses/by-nc/3.0/',
        'creative commons: attribution - non commercial - share alike 3.0' => 'https://creativecommons.org/licenses/by-nc-sa/3.0/',
        'creative commons: attribution - non derived work - non commercial 3.0' => 'https://creativecommons.org/licenses/by-nc-nd/3.0/',
        'creative commons: attribution 2.5' => 'https://creativecommons.org/licenses/by/2.5/',
        'creative commons: attribution - share alike 2.5' => 'https://creativecommons.org/licenses/by-sa/2.5/',
        'creative commons: attribution - non derived work 2.5' => 'https://creativecommons.org/licenses/by-nd/2.5/',
        'creative commons: attribution - non commercial 2.5' => 'https://creativecommons.org/licenses/by-nc/2.5/',
        'creative commons: attribution - non commercial - share alike 2.5' => 'https://creativecommons.org/licenses/by-nc-sa/2.5/',
        'creative commons: attribution - non derived work - non commercial 2.5' => 'https://creativecommons.org/licenses/by-nc-nd/2.5/',
    ];

    /*********************************************************************************
     * OPTIONS - PROPERTIES
     *********************************************************************************/

    public const METADATA_TYPES = [
        'lom' => 'LOM',
        'lomes' => 'LOM-ES',
    ];

    public const LEARNING_RESOURCE_TYPES = [
        'guided reading' => self::TRANS_PREFIX.'guided reading',
        'conceptual map' => self::TRANS_PREFIX.'conceptual map',
        'master class' => self::TRANS_PREFIX.'master class',
        'textual image analysis' => self::TRANS_PREFIX.'textual-image analysis',
        'discussion activity' => self::TRANS_PREFIX.'discussion activity',
        'exercise' => self::TRANS_PREFIX.'exercise',
        'closed exercise or problem' => self::TRANS_PREFIX.'closed exercise or problem',
        'contextualized case problem' => self::TRANS_PREFIX.'contextualized case problem',
        'open problem' => self::TRANS_PREFIX.'open problem',
        'real or virtual learning environment' => self::TRANS_PREFIX.'real or virtual learning environment',
        'didactic game' => self::TRANS_PREFIX.'didactic game',
        'webquest' => self::TRANS_PREFIX.'webquest',
        'experiment' => self::TRANS_PREFIX.'experiment',
        'real project' => self::TRANS_PREFIX.'real project',
        'simulation' => self::TRANS_PREFIX.'simulation',
        'questionnaire' => self::TRANS_PREFIX.'questionnaire',
        'exam' => self::TRANS_PREFIX.'exam',
        'self assessment' => self::TRANS_PREFIX.'self assessment',
        'diagram' => self::TRANS_PREFIX.'diagram',
        'figure' => self::TRANS_PREFIX.'figure',
        'graph' => self::TRANS_PREFIX.'graph',
        'index' => self::TRANS_PREFIX.'index',
        'slide' => self::TRANS_PREFIX.'slide',
        'table' => self::TRANS_PREFIX.'table',
        'narrative text' => self::TRANS_PREFIX.'narrative text',
        'problem statement' => self::TRANS_PREFIX.'problem statement',
        'lecture' => self::TRANS_PREFIX.'lecture',
    ];

    public const YES_NO_OPTIONS = [
        '' => '-',
        'yes' => self::TRANS_PREFIX.'yes',
        'no' => self::TRANS_PREFIX.'no',
    ];

    public const ACCESS_TYPE_OPTIONS = [
        'universal' => self::TRANS_PREFIX.'universal',
        'non-universal' => self::TRANS_PREFIX.'non-universal',
    ];

    public const INTENDENT_END_USER_OPTIONS = [
        '' => '-',
        'ordinary learner' => self::TRANS_PREFIX.'ordinary learner',
        'special needs learner' => self::TRANS_PREFIX.'special needs learner',
        'gifted learner' => self::TRANS_PREFIX.'gifted learner',
    ];

    public const EDUCATIONAL_USER_ROLE_OPTIONS = [
        'learner' => self::TRANS_PREFIX.'learner',
        'special needs learner' => self::TRANS_PREFIX.'special needs learner',
        'gifted learner' => self::TRANS_PREFIX.'gifted learner',
        'learners late integration into the education system' => self::TRANS_PREFIX.'learners late integration into the education system',
        'learner with other specific educational support needs' => self::TRANS_PREFIX.'learner with other specific educational support needs',
        'general public' => self::TRANS_PREFIX.'general public',
        'individual' => self::TRANS_PREFIX.'individual',
        'group' => self::TRANS_PREFIX.'group',
        'teacher' => self::TRANS_PREFIX.'teacher',
        'tutor' => self::TRANS_PREFIX.'tutor',
        'family' => self::TRANS_PREFIX.'family',
        'information scientist' => self::TRANS_PREFIX.'information scientist',
        'computer scientist' => self::TRANS_PREFIX.'computer scientist',
        'manager' => self::TRANS_PREFIX.'manager',
        'education expert' => self::TRANS_PREFIX.'education expert',
        'subject matter expert' => self::TRANS_PREFIX.'subject matter expert',
    ];

    public const INTENDENT_END_USER_CONTEXT_OPTIONS = [
        '' => '-',
        'classroom' => self::TRANS_PREFIX.'classroom',
        'real environment' => self::TRANS_PREFIX.'real environment',
    ];

    public const INTENDENT_END_USER_MODALITY_OPTIONS = [
        '' => '-',
        'face to face' => self::TRANS_PREFIX.'face to face',
        'blended' => self::TRANS_PREFIX.'blended',
        'distance' => self::TRANS_PREFIX.'distance',
    ];

    public const AGREGATION_LEVELS = [
        '1' => self::TRANS_PREFIX.'resources and integrated resources',
        '2' => self::TRANS_PREFIX.'learning objects',
        '3' => self::TRANS_PREFIX.'teaching sequences',
        '4' => self::TRANS_PREFIX.'training programmes, courses and plans',
        '5' => self::TRANS_PREFIX.'contribution',
    ];

    public const STRUCTURES = [
        '' => '-',
        'atomic' => self::TRANS_PREFIX.'atomic',
        'collection' => self::TRANS_PREFIX.'collection',
        'networked' => self::TRANS_PREFIX.'networked',
        'hierarchical' => self::TRANS_PREFIX.'hierarchical',
        'linear' => self::TRANS_PREFIX.'linear',
    ];

    public const REQUIREMENTS_TYPE = [
        '' => '-',
        'operating system' => self::TRANS_PREFIX.'operating system',
        'browser' => self::TRANS_PREFIX.'browser',
    ];

    public const INTERACTIVE_TYPE = [
        '' => '-',
        'active' => self::TRANS_PREFIX.'active',
        'expositive' => self::TRANS_PREFIX.'expositive',
        'mixed' => self::TRANS_PREFIX.'mixed',
    ];

    public const LIFE_CYCLE_STATUS = [
        '' => '-',
        'draft' => self::TRANS_PREFIX.'draft',
        'final' => self::TRANS_PREFIX.'final',
        'revised' => self::TRANS_PREFIX.'revised',
        'unavailable' => self::TRANS_PREFIX.'unavailable',
    ];

    public const LOM_LIFECYCLE_CONTRIBUTE_ROLE_OPTIONS = [
        '' => '-',
        'author' => self::TRANS_PREFIX.'author',
        'publisher' => self::TRANS_PREFIX.'publisher',
        'unknown' => self::TRANS_PREFIX.'unknown',
        'initiator' => self::TRANS_PREFIX.'initiator',
        'terminator' => self::TRANS_PREFIX.'terminator',
        'validator' => self::TRANS_PREFIX.'validator',
        'editor' => self::TRANS_PREFIX.'editor',
        'graphical designer' => self::TRANS_PREFIX.'graphical designer',
        'technical implementer' => self::TRANS_PREFIX.'technical implementer',
        'content provider' => self::TRANS_PREFIX.'content provider',
        'technical validator' => self::TRANS_PREFIX.'technical validator',
        'educational validator' => self::TRANS_PREFIX.'educational validator',
        'script writer' => self::TRANS_PREFIX.'script writer',
        'instructional designer' => self::TRANS_PREFIX.'instructional designer',
        'subject matter expert' => self::TRANS_PREFIX.'subject matter expert',
    ];

    public const LOM_METAMETADATA_CONTRIBUTE_ROLE_OPTIONS = [
        '' => '-',
        'creator' => self::TRANS_PREFIX.'creator',
        'validator' => self::TRANS_PREFIX.'validator',
    ];

    public const INTERACTIVITY_LEVELS = [
        '' => '-',
        'very low' => self::TRANS_PREFIX.'very low',
        'low' => self::TRANS_PREFIX.'low',
        'medium' => self::TRANS_PREFIX.'medium',
        'high' => self::TRANS_PREFIX.'high',
        'very high' => self::TRANS_PREFIX.'very high',
    ];

    public const DIFFICULTY_LEVELS = [
        '' => '-',
        'very easy' => self::TRANS_PREFIX.'very easy',
        'easy' => self::TRANS_PREFIX.'easy',
        'medium' => self::TRANS_PREFIX.'medium',
        'difficult' => self::TRANS_PREFIX.'difficult',
        'very difficult' => self::TRANS_PREFIX.'very difficult',
    ];

    public const EDUCATION_CONTEXT_OPTIONS = [
        '' => '-',
        'classroom' => self::TRANS_PREFIX.'classroom',
        'laboratory' => self::TRANS_PREFIX.'laboratory',
        'real-environment' => self::TRANS_PREFIX.'real environment',
        'home' => self::TRANS_PREFIX.'home',
        'mixed' => self::TRANS_PREFIX.'mixed',
        'teacher' => self::TRANS_PREFIX.'teacher',
        'family' => self::TRANS_PREFIX.'family',
        'tutor' => self::TRANS_PREFIX.'tutor',
        'schoolmate' => self::TRANS_PREFIX.'schoolmate',
        'independent' => self::TRANS_PREFIX.'independent',
        'blended' => self::TRANS_PREFIX.'blended',
        'presencial' => self::TRANS_PREFIX.'presencial',
        'face to face' => self::TRANS_PREFIX.'face to face',
        'distance' => self::TRANS_PREFIX.'distance',
    ];

    public const COGNITIVE_PROCESS_OPTIONS = [
        '' => '-',
        'analyse' => self::TRANS_PREFIX.'analyse',
        'implement' => self::TRANS_PREFIX.'implement',
        'collaborate' => self::TRANS_PREFIX.'collaborate',
        'compare' => self::TRANS_PREFIX.'compare',
        'share' => self::TRANS_PREFIX.'share',
        'compete' => self::TRANS_PREFIX.'compete',
        'understand' => self::TRANS_PREFIX.'understand',
        'prove' => self::TRANS_PREFIX.'prove',
        'communicate' => self::TRANS_PREFIX.'communicate',
        'contextualize' => self::TRANS_PREFIX.'contextualize',
        'control' => self::TRANS_PREFIX.'control',
        'cooperate' => self::TRANS_PREFIX.'cooperate',
        'create' => self::TRANS_PREFIX.'create',
        'decide' => self::TRANS_PREFIX.'decide',
        'define' => self::TRANS_PREFIX.'define',
        'describe' => self::TRANS_PREFIX.'describe',
        'discuss' => self::TRANS_PREFIX.'discuss',
        'design' => self::TRANS_PREFIX.'design',
        'self assessment' => self::TRANS_PREFIX.'self assessment',
        'explain' => self::TRANS_PREFIX.'explain',
        'extrapolate' => self::TRANS_PREFIX.'extrapolate',
        'innovate' => self::TRANS_PREFIX.'innovate',
        'investigate' => self::TRANS_PREFIX.'investigate',
        'judge' => self::TRANS_PREFIX.'judge',
        'motivate' => self::TRANS_PREFIX.'motivate',
        'observe' => self::TRANS_PREFIX.'observe',
        'organize' => self::TRANS_PREFIX.'organize',
        'organize oneself' => self::TRANS_PREFIX.'organize oneself',
        'plan' => self::TRANS_PREFIX.'plan',
        'practise' => self::TRANS_PREFIX.'practise',
        'produce' => self::TRANS_PREFIX.'produce',
        'recognize' => self::TRANS_PREFIX.'recognize',
        'remember' => self::TRANS_PREFIX.'remember',
        'write up' => self::TRANS_PREFIX.'write up',
        'consider' => self::TRANS_PREFIX.'consider',
        'connect' => self::TRANS_PREFIX.'connect',
        'represent' => self::TRANS_PREFIX.'represent',
        'solve' => self::TRANS_PREFIX.'solve',
        'simulate' => self::TRANS_PREFIX.'simulate',
        'summarize' => self::TRANS_PREFIX.'summarize',
        'value' => self::TRANS_PREFIX.'value',
    ];

    public const CLASSIFICATION_PURPOSE_OPTIONS = [
        '' => '-',
        'discipline' => self::TRANS_PREFIX.'discipline',
        'idea' => self::TRANS_PREFIX.'idea',
        'prerequisite' => self::TRANS_PREFIX.'prerequisite',
        'educational objective' => self::TRANS_PREFIX.'educational objective',
        'accessibility restrictions' => self::TRANS_PREFIX.'accessibility restrictions',
        'educational level' => self::TRANS_PREFIX.'educational level',
        'skill level' => self::TRANS_PREFIX.'skill level',
        'security level' => self::TRANS_PREFIX.'security level',
        'competency' => self::TRANS_PREFIX.'competency',
    ];

    /*********************************************************************************
     * GROUPS
     *********************************************************************************/

    public const GROUPS_TITLE = [
        // To review (see #290) 'properties_package' => self::TRANS_PREFIX.'Package',
        'properties_package' => self::TRANS_PREFIX.'Content metadata',
        'properties_package_description' => self::TRANS_PREFIX.'Description',
        'properties_package_usage' => self::TRANS_PREFIX.'Usage',
        // To review (see #290) 'export' => self::TRANS_PREFIX.'Export',
        'export' => self::TRANS_PREFIX.'Export options',
        'custom_code' => self::TRANS_PREFIX.'Custom code',
        'lom_general' => self::TRANS_PREFIX.'General',
        'lom_general_identifier' => self::TRANS_PREFIX.'Identifier',
        'lom_general_title' => self::TRANS_PREFIX.'Title',
        'lom_general_description' => self::TRANS_PREFIX.'Description',
        'lom_general_language' => self::TRANS_PREFIX.'Language',
        'lom_general_keyword' => self::TRANS_PREFIX.'Keyword',
        'lom_general_coverage' => self::TRANS_PREFIX.'Coverage',
        'lom_general_structure' => self::TRANS_PREFIX.'Structure',
        'lom_general_agregationLevel' => self::TRANS_PREFIX.'Aggregation Level',
        'lom_lifeCycle' => self::TRANS_PREFIX.'Life Cycle',
        'lom_lifeCycle_version' => self::TRANS_PREFIX.'Version',
        'lom_lifeCycle_status' => self::TRANS_PREFIX.'Status',
        'lom_lifeCycle_contribute' => self::TRANS_PREFIX.'Contribution',
        'lom_metaMetadata' => self::TRANS_PREFIX.'Meta-Metadata',
        'lom_metaMetadata_identifier' => self::TRANS_PREFIX.'Identifier',
        'lom_metaMetadata_metadataSchema' => self::TRANS_PREFIX.'Schema',
        'lom_metaMetadata_language' => self::TRANS_PREFIX.'Language',
        'lom_metaMetadata_contribute' => self::TRANS_PREFIX.'Contribution',
        'lom_technical' => self::TRANS_PREFIX.'Technical',
        'lom_technical_format' => self::TRANS_PREFIX.'Format',
        'lom_technical_size' => self::TRANS_PREFIX.'Size',
        'lom_technical_location' => self::TRANS_PREFIX.'Location',
        'lom_technical_requirement' => self::TRANS_PREFIX.'Requirement',
        'lom_technical_installationRemarks' => self::TRANS_PREFIX.'Installation Remarks',
        'lom_technical_otherPlatformRequirements' => self::TRANS_PREFIX.'Other Platforms Requirement',
        'lom_technical_duration' => self::TRANS_PREFIX.'Duration',
        'lom_educational' => self::TRANS_PREFIX.'Educational',
        'lom_educational_interactivityType' => self::TRANS_PREFIX.'Interactivity Type',
        'lom_educational_learningResourceType' => self::TRANS_PREFIX.'Learning Resource Type',
        'lom_educational_interactivityLevel' => self::TRANS_PREFIX.'Interactivity Level',
        'lom_educational_semanticDensity' => self::TRANS_PREFIX.'Semantic Density',
        'lom_educational_intendedEndUserRole' => self::TRANS_PREFIX.'Intended End User Role',
        'lom_educational_context' => self::TRANS_PREFIX.'Context',
        'lom_educational_typicalAgeRange' => self::TRANS_PREFIX.'Typical Age Range',
        'lom_educational_difficulty' => self::TRANS_PREFIX.'Difficulty',
        'lom_educational_typicalLearningTime' => self::TRANS_PREFIX.'Typical Learning Time',
        'lom_educational_description' => self::TRANS_PREFIX.'Description',
        'lom_educational_language' => self::TRANS_PREFIX.'Language',
        'lom_educational_cognitiveProcess' => self::TRANS_PREFIX.'Cognitive Process',
        'lom_rights' => self::TRANS_PREFIX.'Rights',
        'lom_rights_cost' => self::TRANS_PREFIX.'Cost',
        'lom_rights_copyrightAndOtherRestrictions' => self::TRANS_PREFIX.'Copyright And Other Restrictions',
        'lom_rights_description' => self::TRANS_PREFIX.'Description',
        'lom_rights_access' => self::TRANS_PREFIX.'Access',
        'lom_relation' => self::TRANS_PREFIX.'Relation',
        'lom_relation_kind' => self::TRANS_PREFIX.'Kind',
        'lom_relation_resource' => self::TRANS_PREFIX.'Resource',
        'lom_annotation' => self::TRANS_PREFIX.'Annotation',
        'lom_annotation_entity' => self::TRANS_PREFIX.'Entity',
        'lom_annotation_date' => self::TRANS_PREFIX.'Date',
        'lom_annotation_description' => self::TRANS_PREFIX.'Description',
        'lom_classification' => self::TRANS_PREFIX.'Classification',
        'lom_classification_purpose' => self::TRANS_PREFIX.'Purpose',
        'lom_classification_taxonPath' => self::TRANS_PREFIX.'Taxon Path',
        'lom_classification_description' => self::TRANS_PREFIX.'Description',
        'lom_classification_keyword' => self::TRANS_PREFIX.'Keyword',
    ];

    public const PROPERTIES_CATEGORIES_TITLE = [
        'properties' => self::TRANS_PREFIX.'Properties',
        'cataloguing' => self::TRANS_PREFIX.'Cataloguing',
    ];

    /*********************************************************************************
     * PROJECT PROPERTIES - PROPERTIES
     *********************************************************************************/

    // PACKAGE
    public const ODE_PROPERTIES_PACKAGE_CONFIG = [
        'pp_title' => [
            'title' => self::TRANS_PREFIX.'Title',
            'help' => self::TRANS_PREFIX.'The name given to the resource.',
            'alwaysVisible' => true,
            'type' => 'text',
            'category' => 'properties',
            'groups' => ['properties_package'],
        ],
        'pp_lang' => [
            'title' => self::TRANS_PREFIX.'Language',
            'help' => self::TRANS_PREFIX.'Select a language.',
            'value' => null,
            'alwaysVisible' => true,
            'type' => 'select',
            'options' => Settings::PACKAGE_LOCALES,
            'category' => 'properties',
            'groups' => ['properties_package'],
        ],
        'pp_author' => [
            'title' => self::TRANS_PREFIX.'Authorship',
            'help' => self::TRANS_PREFIX.'Primary author/s of the resource.',
            'alwaysVisible' => true,
            'type' => 'text',
            'category' => 'properties',
            'groups' => ['properties_package'],
        ],
        'license' => [
            'title' => self::TRANS_PREFIX.'License',
            'value' => 'creative-commons-attribution-share-alike-4.0',
            'alwaysVisible' => true,
            'type' => 'select',
            'options' => self::LICENSES,
            'category' => 'properties',
            'groups' => ['properties_package'],
        ],
        /*
        'pp_learningResourceType' => [
            'title' => self::TRANS_PREFIX.'Learning Resource Type',
            'alwaysVisible' => true,
            'type' => 'select',
            'options' => self::LEARNING_RESOURCE_TYPES,
            'category' => 'properties',
            'groups' => ['properties_package'],
        ],
        */
        'pp_description' => [
            'title' => self::TRANS_PREFIX.'Description',
            'alwaysVisible' => true,
            'type' => 'textarea',
            'category' => 'properties',
            // To review (see #290) 'groups' => ['properties_package', 'properties_package_description'],
            'groups' => ['properties_package'],
        ],
        /*
        'pp_objectives' => [
            'title' => self::TRANS_PREFIX.'Objectives',
            'alwaysVisible' => true,
            'type' => 'textarea',
            'category' => 'properties',
            'groups' => ['properties_package', 'properties_package_description'],
        ],
        'pp_preknowledge' => [
            'title' => self::TRANS_PREFIX.'Preknowledge',
            'alwaysVisible' => true,
            'type' => 'textarea',
            'category' => 'properties',
            'groups' => ['properties_package', 'properties_package_description'],
        ],
        'pp_intendedEndUserRoleType' => [
            'title' => self::TRANS_PREFIX.'Intended User Role',
            'type' => 'select',
            'options' => self::INTENDENT_END_USER_OPTIONS,
            'category' => 'properties',
            'groups' => ['properties_package', 'properties_package_usage'],
        ],
        'pp_intendedEndUserRoleGroup' => [
            'title' => self::TRANS_PREFIX.'For Group Work',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['properties_package', 'properties_package_usage'],
        ],
        'pp_intendedEndUserRoleTutor' => [
            'title' => self::TRANS_PREFIX.'For Individual Tuition',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['properties_package', 'properties_package_usage'],
        ],
        'pp_contextPlace' => [
            'title' => self::TRANS_PREFIX.'Context',
            'type' => 'select',
            'options' => self::INTENDENT_END_USER_CONTEXT_OPTIONS,
            'category' => 'properties',
            'groups' => ['properties_package', 'properties_package_usage'],
        ],
        'pp_contextMode' => [
            'title' => self::TRANS_PREFIX.'Modality',
            'type' => 'select',
            'options' => self::INTENDENT_END_USER_MODALITY_OPTIONS,
            'category' => 'properties',
            'groups' => ['properties_package', 'properties_package_usage'],
        ],
        */
    ];

    // EXPORT
    public const ODE_PROPERTIES_EXPORT_CONFIG = [
        'exportSource' => [
            'title' => self::TRANS_PREFIX.'Editable export',
            'value' => 'true',
            'help' => self::TRANS_PREFIX.'The exported content will be editable with eXeLearning.',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['export'],
        ],
        'pp_addExeLink' => [
            'title' => self::TRANS_PREFIX.'"Made with eXeLearning" link',
            'value' => 'true',
            'help' => self::TRANS_PREFIX.'Help us spreading eXeLearning. Checking this option, a "Made with eXeLearning" link will be displayed in your pages.',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['export'],
        ],
        'pp_exportElp' => [
            'title' => self::TRANS_PREFIX.'Include a copy of the source file',
            'value' => 'false',
            'help' => self::TRANS_PREFIX.'A copy of the eXeLearning file will be included when exporting the content.',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['export'],
        ],
        'pp_addPagination' => [
            'title' => self::TRANS_PREFIX.'Page counter',
            'value' => 'false',
            'help' => self::TRANS_PREFIX.'A text with the page number will be added on each page.',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['export'],
        ],
        'pp_addSearchBox' => [
            'title' => self::TRANS_PREFIX.'Search bar (Website export only)',
            'value' => 'false',
            'help' => self::TRANS_PREFIX.'A search box will be added to every page of the website.',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['export'],
        ],
        'pp_addAccessibilityToolbar' => [
            'title' => self::TRANS_PREFIX.'Accessibility toolbar',
            'value' => 'false',
            'help' => self::TRANS_PREFIX.'The accessibility toolbar allows visitors to manipulate some aspects of your site, such as font and text size.',
            'type' => 'checkbox',
            'category' => 'properties',
            'groups' => ['export'],
        ],
        'pp_extraHeadContent' => [
            'title' => self::TRANS_PREFIX.'HEAD',
            'help' => self::TRANS_PREFIX.'HTML to be included at the end of HEAD: LINK, META, SCRIPT, STYLE...',
            'alwaysVisible' => true,
            'type' => 'textarea',
            'category' => 'properties',
            'groups' => ['custom_code'],
        ],
        'footer' => [
            'title' => self::TRANS_PREFIX.'Page footer',
            'help' => self::TRANS_PREFIX.'Type any HTML. It will be placed after every page content. No JavaScript code will be executed inside eXe.',
            'alwaysVisible' => true,
            'type' => 'textarea',
            'category' => 'properties',
            'groups' => ['custom_code'],
        ],
    ];

    // PROPERTIES TAB CONFIG
    public const ODE_PROPERTIES_CONFIG = [
        'properties' => self::ODE_PROPERTIES_PACKAGE_CONFIG,
        'export' => self::ODE_PROPERTIES_EXPORT_CONFIG,
    ];

    /*********************************************************************************
     * PROJECT PROPERTIES - CATALOGUING
     *********************************************************************************/

    // METADATA TYPE
    public const ODE_CATALOGUING_STANDARD_CONFIG = [
        'pp_exportMetadataType' => [
            'title' => self::TRANS_PREFIX.'Cataloging standard',
            'required' => true,
            'onchange' => 'lom_metaMetadata_metadataSchema',
            'value' => 'lomes',
            'type' => 'select',
            'options' => self::METADATA_TYPES,
            'category' => 'cataloguing',
        ],
    ];

    // GENERAL
    public const ODE_CATALOGUING_GENERAL_CONFIG = [
        'lom_general_identifier_catalog' => [
            'title' => self::TRANS_PREFIX.'Catalog',
            'required' => true,
            'value' => Constants::ELP_PROPERTIES_NO_CATALOG_NAME,
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_identifier'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_general_identifier_entry' => [
            'title' => self::TRANS_PREFIX.'Entry',
            'required' => true,
            'value' => 'ODE-{odeSessionId}',
            'generated' => true,
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_identifier'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_general_title_string' => [
            'title' => self::TRANS_PREFIX.'Title',
            'required' => true,
            'lang' => 'lom_general_title_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_title'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_general_title_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_title'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_general_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'required' => true,
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_language'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_general_description_string' => [
            'title' => self::TRANS_PREFIX.'Description',
            'required' => true,
            'lang' => 'lom_general_description_string_language',
            'type' => 'textarea',
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_description'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_general_description_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_description'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_general_keyword_string' => [
            'title' => self::TRANS_PREFIX.'Keyword',
            'lang' => 'lom_general_keyword_string_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_keyword'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_general_keyword_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_keyword'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_general_coverage_string' => [
            'title' => self::TRANS_PREFIX.'Coverage',
            'lang' => 'lom_general_coverage_string_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_coverage'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_general_coverage_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_coverage'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_general_structure_value' => [
            'title' => self::TRANS_PREFIX.'Structure',
            'type' => 'select',
            'options' => self::STRUCTURES,
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_structure'],
        ],
        'lom_general_aggregationLevel_value' => [
            'title' => self::TRANS_PREFIX.'Agregation Level',
            'required' => true,
            'value' => 'learning-objects',
            'type' => 'select',
            'options' => self::AGREGATION_LEVELS,
            'category' => 'cataloguing',
            'groups' => ['lom_general', 'lom_general_agregationLevel'],
        ],
    ];

    // LIFE-CYCLEagregation-level
    public const ODE_CATALOGUING_LIFECYCLE_CONFIG = [
        'lom_lifeCycle_version_string' => [
            'title' => self::TRANS_PREFIX.'Version',
            'lang' => 'lom_lifeCycle_version_string_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_version'],
        ],
        'lom_lifeCycle_version_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_version'],
        ],
        'lom_lifeCycle_status_value' => [
            'title' => self::TRANS_PREFIX.'Status',
            'type' => 'select',
            'options' => self::LIFE_CYCLE_STATUS,
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_status'],
        ],
        'lom_lifeCycle_contribute_role_value' => [
            'title' => self::TRANS_PREFIX.'Role',
            'type' => 'select',
            'options' => self::LOM_LIFECYCLE_CONTRIBUTE_ROLE_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_contribute'],
            'duplicate' => 5,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_lifeCycle_contribute_entity_name' => [
            'title' => self::TRANS_PREFIX.'Name',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_contribute'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_lifeCycle_contribute_entity_organization' => [
            'title' => self::TRANS_PREFIX.'Organization',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_contribute'],
            'multiple' => true,
            'index' => 2,
        ],
        'lom_lifeCycle_contribute_entity_email' => [
            'title' => self::TRANS_PREFIX.'Email',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_contribute'],
            'multiple' => true,
            'index' => 3,
        ],
        'lom_lifeCycle_contribute_date' => [
            'title' => self::TRANS_PREFIX.'Date',
            'type' => 'date',
            'category' => 'cataloguing',
            'groups' => ['lom_lifeCycle', 'lom_lifeCycle_contribute'],
            'multiple' => true,
            'index' => 4,
        ],
    ];

    // META-METADATA
    public const ODE_CATALOGUING_METAMETADATA_CONFIG = [
        'lom_metaMetadata_identifier_catalog' => [
            'title' => self::TRANS_PREFIX.'Catalog',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_identifier'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_metaMetadata_identifier_entry' => [
            'title' => self::TRANS_PREFIX.'Entry',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_identifier'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_metaMetadata_metadataSchema' => [
            'title' => self::TRANS_PREFIX.'Metadata Schema',
            'required' => true,
            'value' => 'lomes',
            'lang' => 'lom_metaMetadata_language',
            'type' => 'text',
            'readonly' => true,
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_metadataSchema'],
        ],
        'lom_metaMetadata_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'required' => true,
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_language'],
        ],
        'lom_metaMetadata_contribute_role_value' => [
            'title' => self::TRANS_PREFIX.'Role',
            'type' => 'select',
            'options' => self::LOM_METAMETADATA_CONTRIBUTE_ROLE_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_contribute'],
            'duplicate' => 5,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_metaMetadata_contribute_entity_name' => [
            'title' => self::TRANS_PREFIX.'Name',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_contribute'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_metaMetadata_contribute_entity_organization' => [
            'title' => self::TRANS_PREFIX.'Organization',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_contribute'],
            'multiple' => true,
            'index' => 2,
        ],
        'lom_metaMetadata_contribute_entity_email' => [
            'title' => self::TRANS_PREFIX.'Email',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_contribute'],
            'multiple' => true,
            'index' => 3,
        ],
        'lom_metaMetadata_contribute_date' => [
            'title' => self::TRANS_PREFIX.'Date',
            'type' => 'date',
            'category' => 'cataloguing',
            'groups' => ['lom_metaMetadata', 'lom_metaMetadata_contribute'],
            'multiple' => true,
            'index' => 4,
        ],
    ];

    // TECHNICAL
    public const ODE_CATALOGUING_TECHNICAL_CONFIG = [
        'lom_technical_format' => [
            'title' => self::TRANS_PREFIX.'Format',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_format'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_technical_size' => [
            'title' => self::TRANS_PREFIX.'Size',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_size'],
        ],
        'lom_technical_location' => [
            'title' => self::TRANS_PREFIX.'Location',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_location'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_technical_requirement_orComposite_type_value' => [
            'title' => self::TRANS_PREFIX.'Type',
            'type' => 'select',
            'options' => self::REQUIREMENTS_TYPE,
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_requirement'],
            'duplicate' => 4,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_technical_requirement_orComposite_name_value' => [
            'title' => self::TRANS_PREFIX.'Name',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_requirement'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_technical_requirement_orComposite_minimumVersion' => [
            'title' => self::TRANS_PREFIX.'Minimum version',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_requirement'],
            'multiple' => true,
            'index' => 2,
        ],
        'lom_technical_requirement_orComposite_maximumVersion' => [
            'title' => self::TRANS_PREFIX.'Maximum version',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_requirement'],
            'multiple' => true,
            'index' => 3,
        ],
        'lom_technical_installationRemarks_string' => [
            'title' => self::TRANS_PREFIX.'Installation remarks',
            'lang' => 'lom_technical_installationRemarks_string_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_installationRemarks'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_technical_installationRemarks_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_installationRemarks'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_technical_otherPlatformRequirements_string' => [
            'title' => self::TRANS_PREFIX.'Other platforms requirements',
            'lang' => 'lom_technical_otherPlatformRequirements_string_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_otherPlatformRequirements'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_technical_otherPlatformRequirements_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_otherPlatformRequirements'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_technical_duration_years' => [
            'title' => self::TRANS_PREFIX.'Years',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_duration'],
        ],
        'lom_technical_duration_months' => [
            'title' => self::TRANS_PREFIX.'Months',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_duration'],
        ],
        'lom_technical_duration_days' => [
            'title' => self::TRANS_PREFIX.'Days',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_duration'],
        ],
        'lom_technical_duration_hours' => [
            'title' => self::TRANS_PREFIX.'Hours',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_duration'],
        ],
        'lom_technical_duration_minutes' => [
            'title' => self::TRANS_PREFIX.'Minutes',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_duration'],
        ],
        'lom_technical_duration_seconds' => [
            'title' => self::TRANS_PREFIX.'Seconds',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_technical', 'lom_technical_duration'],
        ],
    ];

    // EDUCATIONAL
    public const ODE_CATALOGUING_EDUCATIONAL_CONFIG = [
        'lom_educational_interactivityType_value' => [
            'title' => self::TRANS_PREFIX.'Interactive Type',
            'type' => 'select',
            'options' => self::INTERACTIVE_TYPE,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_interactivityType'],
        ],
        'lom_educational_learningResourceType_value' => [
            'title' => self::TRANS_PREFIX.'Learning Resource Type',
            'required' => true,
            'type' => 'select',
            'options' => self::LEARNING_RESOURCE_TYPES,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_learningResourceType'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_educational_interactivityLevel_value' => [
            'title' => self::TRANS_PREFIX.'Interactivity Level',
            'type' => 'select',
            'options' => self::INTERACTIVITY_LEVELS,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_interactivityLevel'],
        ],
        'lom_educational_semanticDensity_value' => [
            'title' => self::TRANS_PREFIX.'Semantic Density',
            'type' => 'select',
            'options' => self::INTERACTIVITY_LEVELS,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_semanticDensity'],
        ],
        'lom_educational_intendedEndUserRole_value' => [
            'title' => self::TRANS_PREFIX.'Intended End User Role',
            'type' => 'select',
            'options' => self::EDUCATIONAL_USER_ROLE_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_intendedEndUserRole'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_educational_context_value' => [
            'title' => self::TRANS_PREFIX.'Context',
            'type' => 'select',
            'options' => self::EDUCATION_CONTEXT_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_context'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_educational_typicalAgeRange_string' => [
            'title' => self::TRANS_PREFIX.'Typical Age Range',
            'lang' => 'lom_educational_typicalAgeRange_string_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalAgeRange'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_educational_typicalAgeRange_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalAgeRange'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_educational_difficulty_value' => [
            'title' => self::TRANS_PREFIX.'Difficulty',
            'type' => 'select',
            'options' => self::DIFFICULTY_LEVELS,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_difficulty'],
        ],
        'lom_educational_typicalLearningTime_years' => [
            'title' => self::TRANS_PREFIX.'Years',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalLearningTime'],
        ],
        'lom_educational_typicalLearningTime_months' => [
            'title' => self::TRANS_PREFIX.'Months',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalLearningTime'],
        ],
        'lom_educational_typicalLearningTime_days' => [
            'title' => self::TRANS_PREFIX.'Days',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalLearningTime'],
        ],
        'lom_educational_typicalLearningTime_hours' => [
            'title' => self::TRANS_PREFIX.'Hours',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalLearningTime'],
        ],
        'lom_educational_typicalLearningTime_minutes' => [
            'title' => self::TRANS_PREFIX.'Minutes',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalLearningTime'],
        ],
        'lom_educational_typicalLearningTime_seconds' => [
            'title' => self::TRANS_PREFIX.'Seconds',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_typicalLearningTime'],
        ],
        'lom_educational_description_string' => [
            'title' => self::TRANS_PREFIX.'Description',
            'lang' => 'lom_educational_description_string_language',
            'type' => 'textarea',
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_description'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_educational_description_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_description'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_educational_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'required' => true,
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_language'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_educational_cognitiveProcess_value' => [
            'title' => self::TRANS_PREFIX.'Cognitive Process',
            'type' => 'select',
            'options' => self::COGNITIVE_PROCESS_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_educational', 'lom_educational_cognitiveProcess'],
            'duplicate' => 1,
            'multiple' => true,
            'index' => 0,
        ],
    ];

    // RIGHTS
    public const ODE_CATALOGUING_RIGHTS_CONFIG = [
        'lom_rights_cost_value' => [
            'title' => self::TRANS_PREFIX.'Cost',
            'type' => 'select',
            'options' => self::YES_NO_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_rights', 'lom_rights_cost'],
        ],
        'lom_rights_copyrightAndOtherRestrictions_value' => [
            'title' => self::TRANS_PREFIX.'Copyright And Other Restrictions',
            'required' => true,
            'type' => 'select',
            'options' => self::LICENSES,
            'category' => 'cataloguing',
            'groups' => ['lom_rights', 'lom_rights_copyrightAndOtherRestrictions'],
        ],
        'lom_rights_description_string' => [
            'title' => self::TRANS_PREFIX.'Description',
            'lang' => 'lom_rights_description_string_language',
            'type' => 'textarea',
            'category' => 'cataloguing',
            'groups' => ['lom_rights', 'lom_rights_description'],
            'duplicate' => 2,
            'multiple' => true,
            'index' => 0,
        ],
        'lom_rights_description_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_rights', 'lom_rights_description'],
            'multiple' => true,
            'index' => 1,
        ],
        'lom_rights_access_accessType_value' => [
            'title' => self::TRANS_PREFIX.'Access Type',
            'required' => true,
            'value' => 'universal',
            'type' => 'select',
            'options' => self::ACCESS_TYPE_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_rights', 'lom_rights_access'],
        ],
    ];

    // RELATION
    public const ODE_CATALOGUING_RELATION_CONFIG = [
        'lom_relation_kind_value' => [
            'title' => self::TRANS_PREFIX.'Kind',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_relation', 'lom_relation_kind'],
        ],
        'lom_relation_resource_identifier_catalog' => [
            'title' => self::TRANS_PREFIX.'Catalog',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_relation', 'lom_relation_resource'],
        ],
        'lom_relation_resource_identifier_entry' => [
            'title' => self::TRANS_PREFIX.'Entry',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_relation', 'lom_relation_resource'],
        ],
    ];

    // ANNOTATION
    public const ODE_CATALOGUING_ANNOTATION_CONFIG = [
        'lom_annotation_entity_name' => [
            'title' => self::TRANS_PREFIX.'Name',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_annotation', 'lom_annotation_entity'],
        ],
        'lom_annotation_entity_organization' => [
            'title' => self::TRANS_PREFIX.'Organization',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_annotation', 'lom_annotation_entity'],
        ],
        'lom_annotation_entity_email' => [
            'title' => self::TRANS_PREFIX.'Email',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_annotation', 'lom_annotation_entity'],
        ],
        'lom_annotation_date' => [
            'title' => self::TRANS_PREFIX.'Date',
            'type' => 'date',
            'category' => 'cataloguing',
            'groups' => ['lom_annotation', 'lom_annotation_date'],
        ],
        'lom_annotation_description_string' => [
            'title' => self::TRANS_PREFIX.'Description',
            'lang' => 'lom_annotation_description_string_language',
            'type' => 'textarea',
            'category' => 'cataloguing',
            'groups' => ['lom_annotation', 'lom_annotation_description'],
            'duplicate' => 2,
            'multiple' => true,
        ],
        'lom_annotation_description_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_annotation', 'lom_annotation_description'],
            'multiple' => true,
        ],
    ];

    // CLASSIFICATION
    public const ODE_CATALOGUING_CLASSIFICATION_CONFIG = [
        'lom_classification_purpose_value' => [
            'title' => self::TRANS_PREFIX.'Purpose',
            'type' => 'select',
            'options' => self::CLASSIFICATION_PURPOSE_OPTIONS,
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_purpose'],
        ],
        'lom_classification_taxonPath_source_string' => [
            'title' => self::TRANS_PREFIX.'Source',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_taxonPath'],
        ],
        'lom_classification_taxonPath_taxon_entry_string' => [
            'title' => self::TRANS_PREFIX.'Taxon',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_taxonPath'],
            'duplicate' => 3,
            'multiple' => true,
        ],
        'lom_classification_taxonPath_taxon_id' => [
            'title' => self::TRANS_PREFIX.'ID',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_taxonPath'],
            'multiple' => true,
        ],
        'lom_classification_taxonPath_taxon_entry_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_taxonPath'],
            'multiple' => true,
        ],
        'lom_classification_description_string' => [
            'title' => self::TRANS_PREFIX.'Description',
            'lang' => 'lom_classification_description_string_language',
            'type' => 'textarea',
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_description'],
            'duplicate' => 2,
            'multiple' => true,
        ],
        'lom_classification_description_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_description'],
            'multiple' => true,
        ],
        'lom_classification_keyword_string' => [
            'title' => self::TRANS_PREFIX.'Keyword',
            'lang' => 'lom_classification_keyword_string_language',
            'type' => 'text',
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_keyword'],
            'duplicate' => 2,
            'multiple' => true,
        ],
        'lom_classification_keyword_string_language' => [
            'title' => self::TRANS_PREFIX.'Language',
            'value' => null,
            'type' => 'select',
            'options' => Settings::LOCALES,
            'category' => 'cataloguing',
            'groups' => ['lom_classification', 'lom_classification_keyword'],
            'multiple' => true,
        ],
    ];

    // CATALOGUING TAB CONFIG
    public const ODE_CATALOGUING_CONFIG = [
        // 'standard' => self::ODE_CATALOGUING_STANDARD_CONFIG,
        // 'general' => self::ODE_CATALOGUING_GENERAL_CONFIG,
        // 'lifecycle' => self::ODE_CATALOGUING_LIFECYCLE_CONFIG,
        // 'metametadata' => self::ODE_CATALOGUING_METAMETADATA_CONFIG,
        // 'technical' => self::ODE_CATALOGUING_TECHNICAL_CONFIG,
        // 'educational' => self::ODE_CATALOGUING_EDUCATIONAL_CONFIG,
        // 'rights' => self::ODE_CATALOGUING_RIGHTS_CONFIG,
        // 'relation' => self::ODE_CATALOGUING_RELATION_CONFIG,
        // 'annotation' => self::ODE_CATALOGUING_ANNOTATION_CONFIG,
        // 'classification' => self::ODE_CATALOGUING_CLASSIFICATION_CONFIG,
    ];

    /*********************************************************************************
     * PROPERTIES RELATIONS
     *********************************************************************************/

    // Property relationships: Properties -> Cataloguing
    public const PROPERTIES_SIBLING_KEYS_BY_PROPERTIES = [
        'pp_title' => 'lom_general_title_string',
        'pp_lang' => 'lom_general_language',
        'pp_description' => 'lom_general_description_string',
        'license' => 'lom_rights_copyrightAndOtherRestrictions_value',
        'pp_learningResourceType' => 'lom_educational_learningResourceType_value',
    ];

    // Property relationships: Cataloguing -> Properties
    public const PROPERTIES_SIBLING_KEYS_BY_CATALOGUING = [
        'lom_general_title_string' => 'pp_title',
        'lom_general_language' => 'pp_lang',
        'lom_general_description_string' => 'pp_description',
        'lom_rights_copyrightAndOtherRestrictions_value' => 'license',
        'lom_educational_learningResourceType_value' => 'pp_learningResourceType',
    ];

    // Property relationships: Cataloguing metadata type (unidirectional)
    public const CATALOGUING_METADATA_TYPE_UNIDIRECTIONAL_SIBLING_KEYS = [
        'pp_exportMetadataType' => 'lom_metaMetadata_metadataSchema',
    ];

    // Properties default -> preferences relationships
    public const USER_PREFERENCES_DEFAULT_PROPERTIES_RELATION = [
        'license' => 'defaultLicense',
        'lom_rights_copyrightAndOtherRestrictions_value' => 'defaultLicense',
    ];
}
