<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site-Core',
    'description' => 'Core-related management here. Whether frontend or backend, Event or Middleware registration, RTE manipulation or anything else.',
    'version' => '2.2.6',
    'category' => 'plugin',
    'author' => 'Mati',
    'author_email' => 'mati_01@icloud.com',
    'state' => 'stable',

    'constraints' => [
        'conflicts' => [],
        'suggests' => [],

        'depends' => [
            'typo3' => '10.4.20-10.4.99',
            'container' => '*',
        ],
    ],
];
