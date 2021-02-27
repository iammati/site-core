<?php

declare(strict_types=1);

namespace Site\Core\Form\Fields;

use Site\Core\Utility\ExceptionUtility;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Field
{
    /**
     * @var string
     */
    protected static $configurationRootPath = '/Configuration/Fields/';

    /**
     * Creation of field which handles the entire field-array.
     *
     * @param string $fieldIdentifier The identifier of the field e.g. 'Input'.
     * @param array  $config          A TCA field configuration-array.
     *
     * @return array
     */
    public static function create(string $fieldIdentifier, array $config)
    {
        $pathToField = self::$configurationRootPath.$fieldIdentifier.'.php';

        $extKeys = [
            env('CORE_EXT'),
            env('BACKEND_EXT'),
        ];

        $fieldConfigFile = null;
        $finder = new Finder();

        foreach ($extKeys as $extKey) {
            $extFieldsPath = ExtensionManagementUtility::extPath($extKey, self::$configurationRootPath);
            $fieldExtPath = ExtensionManagementUtility::extPath($extKey, $pathToField);

            if (file_exists($fieldExtPath)) {
                $finder->files()->in($extFieldsPath)->name($fieldIdentifier.'.php');

                if ($finder->hasResults()) {
                    foreach ($finder as $file) {
                        $fieldConfigFile = $file;
                    }
                }
            }
        }

        if ($fieldConfigFile === null) {
            ExceptionUtility::throw(
                sprintf(
                    'Field - Could not create field of identifier-type: "%s"'.
                    "\n".'There was no configuration file found - searched node-path were %s',
                    $fieldIdentifier,
                    'EXT:*'.$pathToField
                )
            );
        }

        $fieldConfiguration = include $fieldConfigFile;

        return $fieldConfiguration;
    }
}
