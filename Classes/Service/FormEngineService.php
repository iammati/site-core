<?php

declare(strict_types=1);

namespace Site\Core\Service;

class FormEngineService
{
    /**
     * Registers a new node for the FormEngine.
     */
    public static function register(string $nodeName, int $priority, $class, int $time)
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][$time] = [
            'nodeName' => $nodeName,
            'priority' => $priority,
            'class' => $class,
        ];
    }
}
