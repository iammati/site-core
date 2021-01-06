<?php

declare(strict_types=1);

namespace Site\Core\Helper;

use Site\Core\Utility\StrUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigHelper
{
    /**
     * Fetch a configuration provided inside EXT:$extKey/Configuration/Config.php.
     *
     * @param string $extKey Could be e.g. 'site_backend'
     * @param string $cfgKey While this can be either a single array-like-key ($arr['myKey']) or using a dot-notation e.g. my.awesome.key
     *                       would be parsed as $arr['my']['awesome']['key'].
     *
     * @return mixed
     */
    public static function get(string $extKey, string $cfgKey = '')
    {
        $path = ExtensionManagementUtility::extPath($extKey, 'Configuration/Config.php');

        if (!file_exists($path)) {
            throw new \Exception('It seems that the config file for EXT:'.$extKey.'/Configuration/Config.php is missing. Make sure it exists.');
        }

        $cfg = require $path;

        if ($cfgKey != '') {
            if (StrUtility::contains($cfgKey, '.')) {
                $keys = GeneralUtility::trimExplode('.', $cfgKey);

                foreach ($keys as $key) {
                    $cfg = $cfg[$key];
                }

                return $cfg;
            }

            return $cfg[$cfgKey] ?? false;
        }

        return $cfg;
    }
}
