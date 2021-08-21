<?php

namespace Site\Core\Form\Fields;

use Site\Core\Interfaces\FieldInterface;

class Check extends Field implements FieldInterface
{
    public static function make(string $label, array $fieldConfig = [])
    {
        $fieldIdentifier = basename(__FILE__, '.php');

        return self::create($fieldIdentifier, [
            'label' => $label,
            'fieldConfig' => $fieldConfig,
        ]);
    }
}
