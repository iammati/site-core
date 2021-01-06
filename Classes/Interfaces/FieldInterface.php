<?php

declare(strict_types=1);

namespace Site\Core\Interfaces;

interface FieldInterface
{
    /**
     * The make-method is the init-calling method of the create
     * for the AbstractField-class.
     *
     * @param string $label       Name of the field displayed in the BE.
     * @param array  $fieldConfig Optional. An additional field-configuration.
     *
     * @see \Site\Core\Form\Fields\AbstractField
     *
     * @return void
     */
    public static function make(string $label, array $fieldConfig = []);
}
