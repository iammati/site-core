<?php

use Site\Core\Utility\ExceptionUtility;
use Site\Core\Utility\FieldUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$fieldName = $config['fieldConfig']['fieldName'] ?? ExceptionUtility::throw(
    'The field "Image" requires a configured "fieldName"-fieldConfig value.',
    1628330060
);

$minItems = $config['fieldConfig']['minItems'] ?? 0;
$maxItems = $config['fieldConfig']['maxItems'] ?? 1;

$fileExtensions = $config['fieldConfig']['fileExtensions'] ??
    (
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['fileExtensions'][$fieldName] ??
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
    )
;

unset($config['fieldConfig']['fieldName'], $config['fieldConfig']['minItems'], $config['fieldConfig']['maxItems']);

$cropVariants = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['cropVariants']['default'];

if (
    isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['cropVariants'][$fieldName]) &&
    null !== $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['cropVariants'][$fieldName]
) {
    $cropVariants = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['cropVariants'][$fieldName];
}

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'File',

    'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
        $fieldName,
        [
            'minitems' => $minItems,
            'maxitems' => $maxItems,

            'appearance' => [
                'createNewRelationLinkTitle' => 'Add File',
            ],

            'overrideChildTca' => [
                'columns' => [
                    'crop' => [
                        'config' => [
                            'cropVariants' => $cropVariants,
                        ],
                    ],
                ],

                'uid_local' => [
                    'config' => [
                        'appearance' => [
                            'elementBrowserType' => 'file',
                            'elementBrowserAllowed' => $fileExtensions,
                        ],
                    ],
                ],

                'types' => [
                    '0' => [
                        'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette
                        ',
                    ],

                    File::FILETYPE_TEXT => [
                        'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette
                        ',
                    ],

                    File::FILETYPE_IMAGE => [
                        'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette
                        ',
                    ],

                    File::FILETYPE_AUDIO => [
                        'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                                --palette--;;filePalette
                        ',
                    ],

                    File::FILETYPE_VIDEO => [
                        'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                                --palette--;;filePalette
                        ',
                    ],

                    File::FILETYPE_APPLICATION => [
                        'showitem' => '
                            --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette
                        ',
                    ],
                ],
            ],
        ],
        $fileExtensions
    ),
], $config);
