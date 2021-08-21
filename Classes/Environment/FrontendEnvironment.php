<?php

declare(strict_types=1);

namespace Site\Core\Environment;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontendEnvironment implements SingletonInterface
{
    private $tsfe;

    public function __construct(Tsfe $tsfe = null)
    {
        $this->tsfe = $tsfe ?? GeneralUtility::makeInstance(Tsfe::class);
    }

    /**
     * Initializes the TSFE for a given page ID and language.
     *
     * @param int $pageId
     * @param int $language
     *
     * @throws SiteNotFoundException
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     * @throws \TYPO3\CMS\Core\Http\ImmediateResponseException
     */
    public function initializeTsfe($pageId, $language = 0)
    {
        $this->tsfe->initializeTsfe($pageId, $language);
    }

    public function getConfigurationFromPageId($pageId, $path, $language = 0)
    {
        return $this->typoScript->getConfigurationFromPageId($pageId, $path, $language);
    }

    public function isAllowedPageType(array $pageRecord, $configurationName = 'pages'): bool
    {
        $configuration = $this->getConfigurationFromPageId($pageRecord['uid'], '');
        $allowedPageTypes = $configuration->getIndexQueueAllowedPageTypesArrayByConfigurationName($configurationName);

        return in_array($pageRecord['doktype'], $allowedPageTypes);
    }
}
