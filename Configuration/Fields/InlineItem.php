<?php

return [
    'exclude' => 1,
    'label' => 'Inline-Item',

    'config' => [
        'type' => 'inline',

        'foreign_table' => '{FOREIGN_TABLE}',
        'foreign_field' => 'parentid',
        'foreign_table_field' => 'parenttable',

        'appearance' => [
            'newRecordLinkAddTitle' => 1,
            'collapseAll' => 1,
            'useSortable' => 1,
            'levelLinksPosition' => 'both',
            'showPossibleLocalizationRecords' => 1,
            'showRemovedLocalizationRecords' => 0,
            'showSynchronizationLink' => 1,
            'showAllLocalizationLink' => 1,
            'fileUploadAllowed' => 1,
            'elementBrowserEnabled' => 1,
            'elementBrowserAllowed' => 1,

            'enabledControls' => [
                'info' => 1,
                'new' => 1,
                'dragdrop' => 1,
                'sort' => 1,
                'hide' => 1,
                'delete' => 1,
                'localize' => 1,
            ],
        ],
    ],
];
