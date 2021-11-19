<?php

declare(strict_types=1);

namespace Site\Core\Traits;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait SiteTrait
{
    protected function getCurrentSite(): ?SiteInterface
    {
        try {
            return $this->getSiteFinder()->getSiteByPageId($this->getCurrentPageId());
        } catch (SiteNotFoundException $e) {
            // Do nothing
        }

        return null;
    }

    protected function getSiteFinder(): SiteFinder
    {
        return GeneralUtility::makeInstance(SiteFinder::class);
    }

    protected function getCurrentPageId(): int
    {
	

        return (int) $GLOBALS['TSFE']->id;
    }
}
