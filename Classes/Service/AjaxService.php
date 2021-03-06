<?php

declare(strict_types=1);

namespace Site\Core\Service;

/**
 * Purpose of this service is to register and handle AJAX configurations,
 * to have all configs managed at once rather than per-extension.
 *
 * ===
 *
 * Example:
 * URI: '/ajax?vendor=TxProducts&ajax=Products-*' calls Ajax\ProductsAjax->*
 * where the * could be anything as a string given - e.g. 'index'.
 *
 * @author Mati <mati_01@icloud.com>
 */
class AjaxService
{
    /**
     * Registers an AJAX-configuration with a custom identifier.
     * Optionally also overrides if the third parameter ($override) is set to true.
     *
     * @return void
     */
    public function register(string $identifier, array $config, bool $override = false)
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'][$identifier]) && !$override) {
            return false;
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'][$identifier] = $config;
    }

    /**
     * Finder which returns, if present, the AJAX-configuration by its identifier.
     * If not found it returns by default null.
     *
     * @return void
     */
    public function findByIdentifier(string $identifier)
    {
        $config = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'][$identifier];

        return !isset($config) ? null : $config;
    }

    /**
     * Finder which returns all AJAX-configurations.
     *
     * @return array
     */
    public function findAll()
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'];
    }
}
