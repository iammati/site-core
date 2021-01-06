<?php

use Site\Core\Utility\FieldUtility;

return FieldUtility::createByConfig([
    'exclude' => 0,
    'label' => 'Flex',

    'config' => [
        'type' => 'flex',
        'ds' => [],
    ],
], $config);
