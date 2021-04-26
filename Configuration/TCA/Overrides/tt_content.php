<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'ctypeNameField' => [
        'label' => 'Content-Element',

        'config' => [
            'type' => 'user',
            'renderType' => 'ctypeNameElement',
        ],
    ],
]);
