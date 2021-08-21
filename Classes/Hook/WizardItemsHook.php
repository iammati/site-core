<?php

declare(strict_types=1);

namespace Site\Core\Hook;

use Site\Core\Service\TcaService;
use TYPO3\CMS\Backend\Wizard\NewContentElementWizardHookInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * This Hook-Listener for the WizardItems
 * adds dynamically by fetching via TcaService all Content-Elements
 * and adds them to the newContentElement wizard automatically.
 *
 * @author Mati <mati_01@icloud.com>
 */
class WizardItemsHook implements NewContentElementWizardHookInterface
{
    public function manipulateWizardItems(&$wizardItems, &$parentObject)
    {
        $backendExt = env('BACKEND_EXT');

        $path = ExtensionManagementUtility::extPath($backendExt, 'Configuration/TCA/Overrides/');
        $CTypes = TcaService::fetchCEs($path, false);

        $customWizardItems = [];

        if (0 !== sizeof($CTypes)) {
            foreach ($CTypes as $CType) {
                $ctypeName = explode('_', $CType)[1];

                $identifiers = 'Backend.ContentElements:'.$ctypeName;
                $locallizedCE = ll($backendExt, $identifiers);

                $elementConfiguration = [
                    'iconIdentifier' => $backendExt.'ce-'.$ctypeName,

                    'title' => $locallizedCE['title'] ?? $CType,
                    'description' => $locallizedCE['description'] ?? '',

                    'saveAndClose' => false,

                    'tt_content_defValues' => [
                        'CType' => $CType,
                    ],

                    'params' => '&defVals[tt_content][CType]='.$CType,
                ];

                $customWizardItems['CustomElements_'.$CType] = $elementConfiguration;
            }

            $wizardItems = $customWizardItems + $wizardItems;

            $wizardItems = [
                'CustomElements' => [
                    'header' => ll($backendExt, 'Backend.ContentElements:tabName'),
                ],
            ] + $wizardItems;
        }
    }
}
