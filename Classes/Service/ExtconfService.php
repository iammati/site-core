<?php

namespace Site\Core\Service;

use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtconfService
{
    /**
     * @var PackageManager
     */
    protected $packageManager;

    public function __construct()
    {
        $this->packageManager = GeneralUtility::makeInstance(PackageManager::class);
    }

    public function initialize()
    {
        $packages = $this->packageManager->getAvailablePackages();

        dd($packages);

        if (null === $packages['container'] || $packages['site_core']) {
        }

        $container = $packages['container'];
        unset($packages['container']);

        array_filter($packages, function ($key) use ($packages) {
            $package = $packages[$key];
            dd($key, $package);
        }, ARRAY_FILTER_USE_KEY);
    }
}
