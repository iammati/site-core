<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'Checkbox',

    'config' => [
        'type' => 'check',
        'default' => '',

        'items' => [
            ['', ''],
        ],
    ],
], $config);
