<?php

declare(strict_types=1);

namespace Site\Core\Parser;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;

class TypoScriptParser
{
    public function ll(array &$params)
    {
        $functionArgument = $params['functionArgument'];

        return ll('site_backend', $functionArgument, $this->getLanguageService());
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
