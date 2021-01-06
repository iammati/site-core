<?php

use Site\Core\Utility\ExceptionUtility;
use Site\Core\Utility\FieldUtility;

$fieldName = $config['fieldConfig']['fieldName'] ?? ExceptionUtility::throw('The field "File" requires a configured "fieldName"-fieldConfig value.');

$minItems = $config['fieldConfig']['minItems'] ?? 0;
$maxItems = $config['fieldConfig']['maxItems'] ?? 1;

$fileExtensions = $config['fieldConfig']['fileExtensions'] ??
    (
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['fileExtensions'][$fieldName] ??
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
    )
;

unset($config['fieldConfig']['fieldName']);
unset($config['fieldConfig']['minItems']);
unset($config['fieldConfig']['maxItems']);

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'File',

    'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
        $fieldName,

        [
            'minitems' => $minItems,
            'maxitems' => $maxItems,

            'appearance' => [
                'createNewRelationLinkTitle' => 'Add File',
            ],
        ],

        $fileExtensions
    ),
], $config);
