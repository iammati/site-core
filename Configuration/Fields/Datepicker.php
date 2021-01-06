<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'Datepicker',

    'config' => [
        'type' => 'input',
        'renderType' => 'datetime',
    ],
], $config);
