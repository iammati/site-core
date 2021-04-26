<?php

(function () {
    if (TYPO3_MODE === 'BE') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][] = Site\Core\Hook\SaveCloseHook::class.'->addButton';

        $GLOBALS['TYPO3_USER_SETTINGS']['columns']['disableSaveShortcut'] = [
            'type' => 'check',
            'label' => 'LLL:EXT:'.env('CORE_EXT').'/Resources/Private/Language/locallang.xml:userSettings.disableSaveShortcut',
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings(
            'disableSaveShortcut',
            'before:resetConfiguration'
        );
    }
})();
