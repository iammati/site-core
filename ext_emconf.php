<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Core',
    'description' => 'Core-related management here. Whether frontend or backend, Event or Middleware registration, RTE manipulation or anything else.',
    'version' => '1.5.6',
    'category' => 'plugin',
    'author' => 'Mati',
    'author_email' => 'mati_01@icloud.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,

    'constraints' => [
        'conflicts' => [],
        'suggests' => [],

        'depends' => [
            'typo3' => '10.4.15-10.4.99',
            'container' => '1.3.0',
        ],
    ],
];
