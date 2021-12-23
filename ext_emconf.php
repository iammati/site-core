<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Site-Core',
    'description' => 'Core Extension for any modern TYPO3 Application',
    'version' => '3.4.4',
    'category' => 'plugin',
    'author' => 'Mati',
    'author_email' => 'mati_01@icloud.com',
    'state' => 'stable',

    'constraints' => [
        'conflicts' => [],
        'suggests' => [],

        'depends' => [
            'typo3' => '11.5.4-11.5.99',
            'container' => '*',
        ],
    ],
];
