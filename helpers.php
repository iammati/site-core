<?php

// To avoid problems using in the CLI the typo3_console composer-package
// the helper-functions will work in any other request-type except the CLI itself.

use Site\Core\Configuration\DotenvLoader;
use Site\Core\Service\LocalizationService;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

if (PHP_SAPI !== 'cli') {
    (new DotenvLoader())->postAutoloadDump();
}

if (!function_exists('ll')) {
    /** @throws Exception */
    function ll(string $extKey, string $locallangLabel, string $twoLetterIsoCode = '')
    {
        /** @var LocalizationService $localizationService */
        $localizationService = GeneralUtility::makeInstance(LocalizationService::class);

        if ($localizationService->has($extKey)) {
            return $localizationService->findByKey($extKey, $locallangLabel, $twoLetterIsoCode);
        }

        new Exception(
            'LocalizationService: The "'.$extKey.'" has not been registered yet thus can\'t access the locallized key for "'.$locallangLabel.'".',
            1628367963
        );
    }
}

if (!function_exists('env')) {
    /**
     * Dotenv helper - usage can be anywhere after the core extension has been loaded.
     * Reads a value by the .env-file from your web-server's root directory.
     */
    function env(string $key): ?string
    {
        $value = getenv($key);

        if (!$value) {
            $value = $_ENV[$key];
        }

        return $value;
    }
}

if (!function_exists('serverRequest')) {
    /**
     * Helper to easier get the current HTTP server request from TYPO3.
     */
    function serverRequest(): ServerRequest
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? GeneralUtility::makeInstance(ServerRequest::class);
    }
}

if (!function_exists('frontend')) {
    /**
     * Helper to easier get the current frontend instance by TYPO3.
     * Formerly known as $GLOBALS['TSFE'].
     */
    function frontend(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}

if (!function_exists('ed')) {
    /**
     * Extbase debugger shorthand.
     */
    function ed(mixed ...$args)
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(...$args);
    }
}

if (!function_exists('edd')) {
    /**
     * Extbase debugger shorthand with die.
     */
    function edd(mixed ...$args): void
    {
        ed($args);

        exit;
    }
}

if (!function_exists('renderView')) {
    /**
     * StandaloneView shorthand depending on TYPO3-Fluid
     * instead of TYPO3's core StandaloneView.
     */
    function renderView(array $rootPaths, string $templatePath, array $data = []): string
    {
        $view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Site\Core\View\FluidStandaloneView::class)->create($rootPaths);

        $view->getTemplatePaths()->setTemplatePathAndFilename(
            \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templatePath)
        );

        $view->assignMultiple($data);

        return $view->render();
    }
}
