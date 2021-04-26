<?php

// To avoid problems using in the CLI the typo3_console composer-package
// the helper-functions will work in any other request-type except the CLI itself.

use Site\Core\Composer\EnvLoader;
use Site\Core\Service\LocalizationService;
use Site\Core\Utility\ExceptionUtility;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(new EnvLoader())->postAutoloadDump();

if (!function_exists('ll')) {
    /**
     * Localization helper. Usage can be anywhere after the core extension has been loaded.
     *
     * @param string $extKey           The extension-key
     * @param string $locallangLabel   The label to be locallized
     * @param string $twoLetterIsoCode The twoLetterIsoCode e.g. 'de' or 'en'
     *
     * @throws ExceptionUtility
     *
     * @return mixed
     */
    function ll(string $extKey, string $locallangLabel, string $langCode = '')
    {
        /** @var LocalizationService $localizationService */
        $localizationService = GeneralUtility::makeInstance(LocalizationService::class);

        if ($localizationService->has($extKey)) {
            return $localizationService->findByKey($extKey, $locallangLabel, $langCode);
        }

        ExceptionUtility::throw('LocalizationService: The "'.$extKey.'" has not been registered yet thus can\'t access the locallized key for "'.$locallangLabel.'".');
    }
}

if (!function_exists('env')) {
    /**
     * Dotenv helper. Usage can be anywhere after the core extension has been loaded.
     * Reads a value by the .env-file from your web-server's root directory.
     *
     * @param string $key
     *
     * @return string|null
     */
    function env(string $key)
    {
        $value = getenv($key);

        if (!$value) {
            $value = $_ENV[$key];
        }

        return $value;
    }
}

if (!function_exists('request')) {
    /**
     * Helper to easier get the current HTTP server request.
     *
     * @return ServerRequest
     */
    function request(): ServerRequest
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? GeneralUtility::makeInstance(ServerRequest::class);
    }
}

if (!function_exists('frontend')) {
    function frontend()
    {
        return $GLOBALS['TSFE'];
    }
}
