<?php

namespace Site\Core\Form\Fields;

use Site\Core\Interfaces\FieldInterface;
use Site\Core\Service\TCAService;

class InlineItem extends Field implements FieldInterface
{
    public static function make(string $label, array $fieldConfig = [])
    {
        return TCAService::findConfigByType('InlineItem', '', $label, $fieldConfig['config']);
    }
}
