<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'Input',

    'config' => [
        'type' => 'input',
    ],
], $config);
