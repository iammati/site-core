<?php

(function () {
    // Defining AJAX into $GLOBALS TYPO3_CONF_VARS
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'] = [];
    }

    // Defining TCA_SERVICE into $GLOBALS TYPO3_CONF_VARS
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE'] = [
            'loadedIRREs' => [],
            'loadedCEs' => [],

            'fileExtensions' => [],
        ];
    }

    // Defining LocalizationService into $GLOBALS TYPO3_CONF_VARS
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['LOCALIZATION_SERVICE'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['LOCALIZATION_SERVICE'] = [];
    }

    // Defining Env-Array into $GLOBALS TYPO3_CONF_VARS
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['ENV_DATA'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['ENV_DATA'] = [];
    }

    // Registration of the custom FormEngineElement 'ctypeNameElement'
    // to display in the 'Content Elemen' tab when editing a CE the current name
    // for a better UI/UX.
    Site\Core\Service\FormEngineService::register(
        'ctypeNameElement',
        40,
        Site\Core\Form\Element\CtypeNameElement::class,
        1607344341
    );

    if (TYPO3_MODE === 'BE') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][] = Site\Core\Hook\ButtonBarHook::class.'->loadRequireJsModule';
    }

    include TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('site_core', 'helpers.php');

    // Automatically registering TypoScript configuration for newContentElement wizard for null TS config by developer
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms']['db_new_content_el']['wizardItemsHook'][] = Site\Core\Hook\WizardItemsHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tsparser.php']['preParseFunc']['ll'] = Site\Core\Parser\TypoScriptParser::class.'->ll';

    // The 'default'-cropVariant for the Image field when using site_core's TCAService
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['cropVariants']['default'] = [
        'desktop' => [
            'title' => 'Desktop',

            'allowedAspectRatios' => [
                'NaN' => [
                    'title' => 'Free',
                    'value' => 0.0,
                ],
            ],
        ],

        'tablet_portrait' => [
            'title' => 'Tablet - Portrait',

            'allowedAspectRatios' => [
                'NaN' => [
                    'title' => 'Free',
                    'value' => 0.0,
                ],
            ],
        ],

        'tablet_landscape' => [
            'title' => 'Tablet - Landscape',

            'allowedAspectRatios' => [
                'NaN' => [
                    'title' => 'Free',
                    'value' => 0.0,
                ],
            ],
        ],

        'mobile' => [
            'title' => 'Mobile',

            'allowedAspectRatios' => [
                'NaN' => [
                    'title' => 'Free',
                    'value' => 0.0,
                ],
            ],
        ],
    ];
})();
