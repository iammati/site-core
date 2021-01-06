<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'Link',

    'config' => [
        'type' => 'input',
        'renderType' => 'inputLink',
    ],
], $config);
