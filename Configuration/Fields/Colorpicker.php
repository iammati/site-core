<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'Colorpicker',

    'config' => [
        'type' => 'input',
        'renderType' => 'colorpicker',
    ],
], $config);
