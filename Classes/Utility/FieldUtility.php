<?php

declare(strict_types=1);

namespace Site\Core\Utility;

class FieldUtility
{
    public static function createByConfig(array $baseConfig, array $config)
    {
        $fieldConfig = $config['fieldConfig'];
        unset($config['fieldConfig']);

        $field = array_merge($baseConfig, $config);
        $field['config'] = array_merge($field['config'], $fieldConfig['config'] ?? $fieldConfig);

        $field['description'] = $fieldConfig['description'] ?: '';

        return $field;
    }
}
