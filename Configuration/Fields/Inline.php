<?php

$columns = implode(',', array_keys($additionalConfig['columns']));

$ctrlArr = [
    'ctrl' => [
        'title' => '{TITLE_FIELD}',
        'label' => '{LABEL_FIELD}',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => 1,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'sortby' => 'sorting',

        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],

        'searchFields' => $columns,
    ],

    'palettes' => [
        'language' => [
            'showitem' => '
                sys_language_uid,
                l10n_parent,
                l10n_diffsource
            ',
        ],

        'timeRestriction' => [
            'showitem' => '
                starttime,
                endtime
            ',
        ],
    ],

    'types' => [
        '1' => [
            'showitem' => '
                --div--;'.$additionalConfig['title'].',
                    '.$columns.',
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
                    --palette--;;timeRestriction,
            ',
        ],
    ],

    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',

            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'default' => 0,

                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
            ],
        ],

        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',

            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',

            'config' => [
                'type' => 'check',

                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],

        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',

            'config' => [
                'type' => 'input',
                'size' => 13,
                'default' => 0,
                'eval' => 'datetime',

                'behaivour' => [
                    'allowLanguageSynchronization' => 1,
                ],
            ],
        ],

        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',

            'config' => [
                'type' => 'input',
                'size' => 13,
                'default' => 0,
                'eval' => 'datetime',

                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2040),
                ],

                'behaivour' => [
                    'allowLanguageSynchronization' => 1,
                ],
            ],
        ],

        'parentid' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',

                'foreign_table' => 'tt_content',
                'foreign_table_where' => 'AND tt_content.pid=###CURRENT_PID### AND tt_content.sys_language_uid IN (-1,###REC_FIELD_sys_language_uid###)',

                'items' => [
                    ['', 0],
                ],
            ],
        ],
    ],
];

$ctrlArr = array_merge_recursive($ctrlArr, $additionalConfig);

return $ctrlArr;
