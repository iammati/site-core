<?php

declare(strict_types=1);

namespace Site\Core\Hook;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Adds the shortcut CTRL+S to the backend to save changes like that.
 * BE-Users might disable this feature in their BE-User settings (uc = userconfiguration).
 *
 * @author Armin Vieweg https://bitbucket.org/t--3/save
 * @author Mati <mati_01@icloud.com>
 */
class ButtonBarHook
{
    public function loadRequireJsModule(array $params): array
    {
        $isDisabled = $GLOBALS['BE_USER']->uc['disableSaveShortcut'] ?? false;

        if (!$isDisabled) {
            /** @var PageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            $path = '/typo3conf/ext/'.env('CORE_EXT').'/Resources/Public/JavaScript';

            $pageRenderer->loadRequireJsModule("{$path}/SaveShortcut.js");
            $pageRenderer->loadRequireJsModule("{$path}/CloseShortcut.js");
            $pageRenderer->loadRequireJsModule("{$path}/ReloadShortcut.js");
        }

        return $params['buttons'];
    }
}
