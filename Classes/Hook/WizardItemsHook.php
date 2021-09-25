<?php

declare(strict_types=1);

namespace Site\Core\Hook;

use Site\Core\Service\TcaService;
use TYPO3\CMS\Backend\Controller\ContentElement\NewContentElementController;
use TYPO3\CMS\Backend\Wizard\NewContentElementWizardHookInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This Hook-Listener for the WizardItems
 * adds dynamically by fetching via TcaService all Content-Elements
 * and adds them to the newContentElement wizard automatically.
 *
 * @author Mati <mati_01@icloud.com>
 */
class WizardItemsHook implements NewContentElementWizardHookInterface
{
    protected PackageManager $packageManager;

    public function __construct()
    {
        $this->packageManager = GeneralUtility::makeInstance(PackageManager::class);
    }

    /**
     * @param array $wizardItems
     * @param NewContentElementController $wizardItems
     */
    public function manipulateWizardItems(&$wizardItems, &$parentObject)
    {
        $txContainerParent = (int)$_GET['tx_container_parent'] ?? null;
        $backendExt = env('BACKEND_EXT');
        
        if (!$this->packageManager->isPackageActive($backendExt)) {
            return $wizardItems;
        }

        $path = ExtensionManagementUtility::extPath($backendExt, 'Configuration/TCA/Overrides/');
        $CTypes = TcaService::fetchCEs($path, false);

        $customWizardItems = [];

        if (count($CTypes) !== 0) {
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

                if ($txContainerParent !== null) {
                    $elementConfiguration['tt_content_defValues']['tx_container_parent'] = $txContainerParent;
                    $elementConfiguration['params'] .= '&defVals[tt_content][tx_container_parent]=' . $txContainerParent;
                }

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
