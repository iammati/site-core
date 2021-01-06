<?php

use Site\Core\Composer\EnvLoader;
use Site\Core\Service\LocalizationService;
use Site\Core\Utility\ExceptionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(new EnvLoader())->postAutoloadDump();

// To avoid problems using in the CLI the typo3_console composer-package
// the helper-functions will work in any other request-type except the CLI itself.
if (!function_exists('ll')) {
    /**
     * Localization helper. Usage can be anywhere after the core extension has been loaded.
     *
     * @param string $extKey         The extension-key
     * @param string $locallangLabel The label to be locallized
     *
     * @throws ExceptionUtility
     *
     * @return string
     */
    function ll(string $extKey, string $locallangLabel)
    {
        /** @var LocalizationService */
        $localizationService = GeneralUtility::makeInstance(LocalizationService::class);

        if ($localizationService->has($extKey)) {
            return $localizationService->findByKey($extKey, $locallangLabel);
        }

        ExceptionUtility::throw('LocalizationService: The "'.$extKey.'" has not been registered yet thus can\'t access the locallized key for "'.$locallangLabel.'".');
    }
}
