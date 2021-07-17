<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site-Core',
    'description' => 'Core-related management here. Whether frontend or backend, Event or Middleware registration, RTE manipulation or anything else.',
    'version' => '3.0.0',
    'category' => 'plugin',
    'author' => 'Mati',
    'author_email' => 'mati_01@icloud.com',
    'state' => 'stable',

    'constraints' => [
        'conflicts' => [],
        'suggests' => [],

        'depends' => [
            'typo3' => '11.3.00-11.3.99',
            'container' => '*',
        ],
    ],
];
