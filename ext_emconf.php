<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site-Core',
    'description' => 'Core-related management here. Whether frontend or backend, Event or Middleware registration, RTE manipulation or anything else.',
    'version' => '2.2.0',
    'category' => 'plugin',
    'author' => 'Mati',
    'author_email' => 'mati_01@icloud.com',
    'state' => 'stable',

    'constraints' => [
        'conflicts' => [],
        'suggests' => [],

        'depends' => [
            'typo3' => '10.4.18-10.4.99',
            'container' => '*',
        ],
    ],
];
