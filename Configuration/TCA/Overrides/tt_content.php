<?php

declare(strict_types=1);

defined('TYPO3_MODE') || die('Access denied.');

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
