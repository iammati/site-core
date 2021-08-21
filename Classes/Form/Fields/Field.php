<?php

declare(strict_types=1);

namespace Site\Core\Form\Fields;

use Site\Core\Utility\ExceptionUtility;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Field
{
    protected static string $configurationRootPath = '/Configuration/Fields/';

    /**
     * @param string $fieldIdentifier The identifier of a field e.g. 'Input'.
     * @param array  $config          a TCA field-configuration
     */
    public static function create(string $fieldIdentifier, array $config): array
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

        if (null === $fieldConfigFile) {
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
