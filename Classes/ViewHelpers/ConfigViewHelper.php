<?php

declare(strict_types=1);

namespace Site\Core\ViewHelpers;

use Site\Core\Helper\ConfigHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ConfigViewHelper extends AbstractViewHelper
{
    /**
     * Initialization of required arguments for this ViewHelper.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('keys', 'string', 'Additional keys separated using "key1.key2.key3" dot-notation.', true);
        $this->registerArgument('extKey', 'string', 'Extension Key provided as its package original name with the underscore.');
    }

    /**
     * The actual render logic whenever this VH gets called.
     * Handles the given arguments, generates and adds at the end.
     *
     * @return mixed|void
     */
    public function render()
    {
        $extKey = $this->arguments['extKey'] ?? getenv('FRONTEND_EXT');

        $keys = GeneralUtility::trimExplode('.', $this->arguments['keys']);
        $config = ConfigHelper::get($extKey);

        foreach ($keys as $key) {
            $config = $config[$key];
        }

        return $config;
    }
}
