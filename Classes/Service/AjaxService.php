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
     */
    public function register(string $identifier, array $config, bool $override = false): bool
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'][$identifier]) && !$override) {
            return false;
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'][$identifier] = $config;

        return true;
    }

    /**
     * Finder which returns, if present, the AJAX-configuration by its identifier.
     * If not found it returns by default null.
     */
    public function findByIdentifier(string $identifier): ?array
    {
        $config = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'][$identifier];

        return !isset($config) ? null : $config;
    }

    /**
     * Finder which returns all AJAX-configurations.
     */
    public function findAll(): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['AJAX'];
    }
}
