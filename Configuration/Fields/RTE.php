<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'RTE',

    'config' => [
        'type' => 'text',
        'enableRichtext' => 1,
    ],
], $config);
