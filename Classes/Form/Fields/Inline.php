<?php

namespace Site\Core\Form\Fields;

use Site\Core\Interfaces\FieldInterface;
use Site\Core\Service\TCAService;

class Inline extends Field implements FieldInterface
{
    public static function make(string $label, array $fieldConfig = [])
    {
        return TCAService::findConfigByType('Inline', basename(__FILE__, '.php'), '', [
            'label' => $fieldConfig['label'],
            'title' => $label,

            'columns' => $fieldConfig['columns'],
        ]);
    }
}
