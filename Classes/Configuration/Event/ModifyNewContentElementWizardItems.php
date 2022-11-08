<?php

declare(strict_types=1);

namespace Site\Core\Configuration\Event;

use Site\Core\Service\TcaService;
use TYPO3\CMS\Backend\Controller\Event\ModifyNewContentElementWizardItemsEvent;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

final class ModifyNewContentElementWizardItems
{
    protected PackageManager $packageManager;

    public function __construct(PackageManager $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function __invoke(ModifyNewContentElementWizardItemsEvent $event): void
    {
        $txContainerParent = isset($_GET['tx_container_parent']) ? (int)$_GET['tx_container_parent'] : null;
        $backendExt = 'site_backend';

        if (!$this->packageManager->isPackageActive($backendExt)) {
            return;
        }

        $path = ExtensionManagementUtility::extPath($backendExt, 'Configuration/TCA/Overrides/');
        $CTypes = TcaService::fetchCEs($path, false);

        if (count($CTypes) === 0) {
            return;
        }

        $customWizardItems = [];
        $wizardItems = $event->getWizardItems();

        foreach ($CTypes as $CType) {
            $ctypeName = explode('_', $CType)[1];

            $identifiers = 'Backend.ContentElements:' . $ctypeName;
            $locallizedCE = ll($backendExt, $identifiers);

            $elementConfiguration = [
                'iconIdentifier' => $backendExt . 'ce-' . $ctypeName,

                'title' => $locallizedCE['title'] ?? $CType,
                'description' => $locallizedCE['description'] ?? '',

                'saveAndClose' => false,

                'tt_content_defValues' => [
                    'CType' => $CType,
                ],

                'params' => '&defVals[tt_content][CType]=' . $CType,
            ];

            if ($txContainerParent !== null) {
                $elementConfiguration['tt_content_defValues']['tx_container_parent'] = $txContainerParent;
                $elementConfiguration['params'] .= '&defVals[tt_content][tx_container_parent]=' . $txContainerParent;
            }

            $customWizardItems['CustomElements_' . $CType] = $elementConfiguration;
        }

        $wizardItems = $customWizardItems + $wizardItems;

        $wizardItems = [
            'CustomElements' => [
                'header' => ll($backendExt, 'Backend.ContentElements:tabName'),
            ],
        ] + $wizardItems;

        $event->setWizardItems($wizardItems);
    }
}
