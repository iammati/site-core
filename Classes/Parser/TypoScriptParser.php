<?php

declare(strict_types=1);

namespace Site\Core\Parser;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;

class TypoScriptParser
{
    public function ll(&$params)
    {
        $functionArgument = $params['functionArgument'];

        $backendExt = getenv('BACKEND_EXT');

        return ll($backendExt, $functionArgument, $this->getLanguageService());
    }

    /**
     * Returns the current BE user.
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Returns the current LanguageService.
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
