<?php

declare(strict_types=1);

namespace Site\Core\Service;

class RteService
{
    /**
     * Registers a new YAML RTE configuration file
     * by simply adding the provided one into the $GLOBALS-array for TYPO3.
     *
     * @param string $extKey       The name of the extension where the YAML file is defined - e.g. 'site_backend'.
     * @param string $yamlFileName the name of the targeted YAML file to be registered
     * @param string $identifier   Optional. Default value of the identifier is 'default'.
     */
    public static function register(string $extKey, string $yamlFileName, string $identifier = 'default'): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets'][$identifier] = 'EXT:'.$extKey.'/Configuration/RTE/'.$yamlFileName.'.yaml';
    }
}
