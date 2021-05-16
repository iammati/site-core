<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site-Core',
    'description' => 'Core-related management here. Whether frontend or backend, Event or Middleware registration, RTE manipulation or anything else.',
    'version' => '1.7.2',
    'category' => 'plugin',
    'author' => 'Mati',
    'author_email' => 'mati_01@icloud.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,

    'constraints' => [
        'conflicts' => [],

        'suggests' => [
            'container' => '*',
        ],

        'depends' => [
            'typo3' => '10.4.16-10.4.99',
            'container' => '*',
        ],
    ],
];
