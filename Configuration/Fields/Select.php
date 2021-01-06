<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'Select',

    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'default' => '',

        'items' => [
            ['', ''],
        ],
    ],
], $config);
