<?php

namespace Site\Core\Form\Fields\Input;

use Site\Core\Form\Fields\Field;
use Site\Core\Interfaces\FieldInterface;

class Link extends Field implements FieldInterface
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
